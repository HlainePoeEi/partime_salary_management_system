<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>test</title>
</head>

<body>
    <div>
        <form action="/test" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="file" name="file">
            <input type="submit" value="Read" name="submit">
        </form>
    </div>
</body>

</html>