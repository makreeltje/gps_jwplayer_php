<html>
<head>
<title>Upload file</title>
</head>
<body>
<form action="upload" method="POST" enctype="multipart/form-data">
<input type="file" name="vtt">
    @csrf
    <br>
    <button type="submit">Upload file</button>
</form>
</body>
</html>