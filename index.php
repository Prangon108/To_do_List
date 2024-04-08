<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/style.css">
    <title>Project Main Page</title>
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
        <h1>Welcome to Our Project To Do List</h1>
        <p>This project is a collaborative effort by students from Wayne State University, majoring in Computer Science.</p>
    </header>
    <section id="progress">
        <h2>Project Progress</h2>
        <p>Our to-do list application is currently in its initial development phase, with the homepage and authors page fully constructed. These pages serve as the foundation of our app, offering users a welcoming interface and insight into the team behind the project. As we continue to develop, we will be adding more features and functionalities to enhance user experience and efficiency in managing tasks.</p>
    </section>
    <footer>
        <!-- Link to Authors Page -->
        <p>Discover more about the authors behind this project by visiting the <a href="authors.php">Authors Page</a>.</p>
        
        <!-- Link to Index.php -->
        <p>Head back to the main application <a href="mainapp.php">here</a>.</p>
    </footer>
</body>
</html>
