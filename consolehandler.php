<?php

include_once "files.php";
$return = ["status" => "OK", "cmd" => null, "note" => null];
function note(string $note) { 
    global $return;
    $return['note'] = $note;
}
// Tahnks GPT
function splitPreservingQuotedStrings(string $input): array {
    // Regex: match quoted strings or unquoted words
    preg_match_all('/
        ("(?:\\\\.|[^"\\\\])*")       # Double-quoted string
        |
        (\'(?:\\\\.|[^\'\\\\])*\')    # Single-quoted string
        |
        (\S+)                         # Unquoted word
    /x', $input, $matches);

    $result = [];

    foreach ($matches[0] as $match) {
        // Remove surrounding quotes if present
        if (
            (str_starts_with($match, '"') && str_ends_with($match, '"')) ||
            (str_starts_with($match, "'") && str_ends_with($match, "'"))
        ) {
            // Remove outer quotes
            $quote = $match[0];
            $inner = substr($match, 1, -1);

            // Unescape quotes and backslashes
            $unescaped = stripcslashes($inner);
            $result[] = $unescaped;
        } else {
            // Regular word
            $result[] = $match;
        }
    }

    return $result;
}
if (!str_contains($_SESSION['asa'], '+#+&+^+-+$%[+#+[+[+@_(+$_&') ||
    $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $return['status'] = "FORBIDDEN";
} else {
    $command = splitPreservingQuotedStrings($_POST['command']);
    $return['cmd'] = $command;
    switch ($command[0]) {
        case "Move":
            $status = FileManager::MoveFile($command[1], $command[2]);
            note("Moved File: $status");
        break;
        case "Copy":
            $status = FileManager::CopyFile($command[1], $command[2]);
            note("Copied File: $status");
        break;
        case "Delete":
            $status = FileManager::DeleteFile($command[1]);
            note("Deleted File: $status");
        break;
        case "Make-Dir":
            $status = FileManager::MakeDir($command[1]);
            note("Made Directory: $status");
        break;
        case "Delete-Dir":
            $status = FileManager::DeleteDir($command[1]);
            note("Made Directory: $status");
        break;
        case "Upload":
            $status = FileManager::UploadFile($command[1], $_FILES);
            note("Uploading? File: $status");
        break;
        default:
            $return['status'] = "INVALID_COMMAND";
        break;
    }
}
echo json_encode($return);