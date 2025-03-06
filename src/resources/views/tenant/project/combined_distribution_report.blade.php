<!DOCTYPE html>
<html>
<head>
    <title>Combined Distribution Report</title>
    <style>
        /* Define CSS styles for the PDF content if needed */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .page {
            width: 100%;
            height: 100%;
            padding: 20px;
            box-sizing: border-box;
            page-break-after: always; /* Add page break after each page */
        }
    </style>
</head>
<body>
@foreach($pdfs as $pdf)
    <div class="page">{!! $pdf !!}</div> <!-- Insert PDF content for each project -->
@endforeach
</body>
</html>
