/** @type {FileManagerSystem} */
let FILE_MAN = null;
/** @type {Object.<string,HTMLElement>} */
let ELEMENTS = {};
/**
 * @callback ElementFunctionCallback
 * @param {HTMLElement} ele
 * 
 * @type {Object.<string, ElementFunctionCallback>}
 */
const ELEMENT_FUNCTIONS = {
    "ReloadButton": (ele) => {
        ele.addEventListener('click', (e) => {
            UpdateFileList();
        });
    }
};
/** @type {RegExp} */
let REGEX_FILTER;
let TOGGLE_STATE = true;

window.addEventListener('DOMContentLoaded', () => {
    // Get all objects
    for (const ele of document.querySelectorAll('.JSObject')) {
        ELEMENTS[ele.id] = ele;
        if (Object.keys(ELEMENT_FUNCTIONS).includes(ele.id))
            ELEMENT_FUNCTIONS[ele.id](ele);
    }
    const toggle = document.getElementById('plusMinusToggle');
    const slider = document.getElementById('slider');

    toggle.addEventListener('click', () => {
        TOGGLE_STATE = !TOGGLE_STATE;
        slider.style.left = TOGGLE_STATE ? '0%' : '50%';
    });
    // Create Manager
    FILE_MAN = new FileManagerSystem();
    UpdateFileList();
});

/**
 * 
 * @param {string} folder 
 * @param {*} info 
 * @returns {HTMLDivElement}
 */
function GetSection(folder, info, hierarchy="", indent=0) {
    const indentation = "\u00A0".repeat(indent*2);
    const return_div = document.createElement('div');
    const folder_span = document.createElement('span');
    folder_span.innerText = indentation + folder + "/";
    return_div.append(folder_span);
    let has_content = false;
    let i = 1;
    for (const [path, val] of Object.entries(info)) {
        if (!val) {
            if (
                (REGEX_FILTER && (
                    (TOGGLE_STATE && REGEX_FILTER.test(path)) || 
                    (!TOGGLE_STATE && !REGEX_FILTER.test(path)))
                ) || !REGEX_FILTER
            ) {
                const span = document.createElement('span');
                const full_path = `${hierarchy}/${path}`;
                span.innerText = indentation + "\u00A0".repeat(2) + path;
                span.addEventListener('click', (e) => {
                    ELEMENTS['CommandInput'].value += full_path;
                });
                span.classList.add('file');
                return_div.append(span);
                has_content = true;
            }
        } else {
            const section = GetSection(path, val, `${hierarchy}/${path}`, indent+1);
            if (section) {
                has_content = true;
                return_div.append(section); 
            }
        }
        i++;
    }
    return (has_content)?return_div:null;
}

async function UpdateFileList() {
    ELEMENTS['FilesDataList'].innerHTML = "";
    if (ELEMENTS['FilterInput'].value !== '')
        REGEX_FILTER = new RegExp(ELEMENTS['FilterInput'].value);
    else REGEX_FILTER = null;
    console.log(REGEX_FILTER);
    const file_struct = await FILE_MAN.GetFileStructure();
    console.log(file_struct);
    if (!file_struct.status) {
        alert("FORBIDDEN");
    } else {
        ELEMENTS['FilesDataList'].append(GetSection('', file_struct.data));
    }
}

class FileManagerSystem {
    constructor() {

    }

    async GetFileStructure() {
        let Return;
        await fetch("/files")
        .then(response => response.json())
        .then(data => {
            console.log("DATA", data);
            Return = data;
        });
        return Return;
    }
}