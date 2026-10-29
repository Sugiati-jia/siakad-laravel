<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $filename }}</title>
    <style>
        body, html { 
            margin: 0; 
            padding: 0; 
            height: 100%; 
            overflow: hidden; 
            background-color: #525659; /* Default PDF viewer background */
        }
        iframe { 
            border: none; 
            width: 100%; 
            height: 100%; 
        }
    </style>
</head>
<body>
    <iframe src="data:application/pdf;base64,{{ $pdfContent }}" title="{{ $filename }}"></iframe>
</body>
</html>
