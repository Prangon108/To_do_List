<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/style.css">
    <title>Authors Page</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    line-height: 1.6;
    color: #333;
    background-color: #f4f4f4;
}

header {
    background: #005A9C;
    color: #ffffff;
    padding-top: 30px;
    padding-bottom: 15px;
    padding-left: 20px;
}

header h1, header p {
    margin: 0;
}

section {
    padding: 20px;
}

#progress, .authors {
    background: #ffffff;
    margin: 20px;
    padding: 20px;
    border-radius: 8px;
}

footer {
    background: #333333;
    color: #ffffff;
    text-align: center;
    padding: 10px;
    position: fixed;
    bottom: 0;
    width: 100%;
}

footer a {
    color: #ffffff;
}

.author {
    margin-bottom: 20px;
}

.author h3 {
    margin-top: 0;
}

.author img {
    max-width: 100px;
    border-radius: 50%;
}

.authors {
    display: grid;
    grid-template-columns: repeat(4, 1fr); 
    gap: 80px;
}

a {
    color: #005A9C;
}

a:hover {
    text-decoration: none;
}

/* Responsive adjustments */
@media screen and (max-width: 768px) {
    .authors {
        grid-template-columns: repeat(2, 1fr); 
    }
}

@media screen and (max-width: 480px) {
    .authors {
        grid-template-columns: 1fr; 
    }
}
    </style>
</head>
<body>
    <header>
        <h1>Meet the Authors</h1>
        <p>Learn more about the team behind the project.</p>
    </header>
    <section class="authors">
        <div class="author">
            <h3>Prangon Talukdar</h3>
            <img src="img/Prangon.jpg" alt="Prangon's Avatar">
            <p>Prangon Talukdar, a Computer Science major at Wayne State University, has achieved a remarkable 4.00 GPA and has been consistently named to the Dean's List from 2021 to 2023. He possesses expertise in programming languages, including Python, JAVA, PHP, C, and C++. </p>
        </div>
        <div class="author">
            <h3>Saifur Sabbir</h3>
            <img src="img/Saifur.jpg" alt="Saifur's Avatar">
            <p>Saifur Sabbir, a Senior in computer science at Wayne State University. He loves taking photos of nature on hikes, and he enjoys playing and watching badminton and cricket.</p>
        </div>
        <div class="author">
            <h3>Fen Zhang</h3>
            <img src="img/fen_image.jpg" alt="Fen's Avatar">
            <p>Fen Zhang is a computer science major student, a TA for BE1200, good at Python, will graduate this upcoming Fall.</p>
        </div>
        <div class="author">
            <h3>Carlos Nunez</h3>
            <img src="img/Carlos.jpg" alt="Carlos's Avatar">
            <p>A junior pursuing Computer Science at Wayne State University, focused on software development and programming.</p>
        </div>
    </section>
    <footer>
        <p>Back to the <a href="index.php">Main Page</a>.</p>
    </footer>
</body>
</html>
