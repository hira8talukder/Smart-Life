<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_token'])) {
    header("Location: login.php");
    exit();
}

// Include header
include 'header.php';

// --- IMPORTANT --- 
// To use the news feature, you need a News API key.
// 1. Go to https://newsapi.org/ and sign up for a free API key.
// 2. Replace 'YOUR_API_KEY' below with your actual API key.
$apiKey = 'YOUR_API_KEY';
$country = 'us'; // Change to your desired country
$url = "https://newsapi.org/v2/top-headlines?country={$country}&apiKey={$apiKey}";

// Fetch news data
$response = @file_get_contents($url);
$newsData = json_decode($response);

?>

<div class="container">
    <h2>Top 10 Daily News</h2>
    <div class="news-container">
        <?php
        if ($newsData && $newsData->status == 'ok') {
            $articles = array_slice($newsData->articles, 0, 10);
            foreach ($articles as $article) {
                echo "<div class='news-article'>";
                echo "<a href='{$article->url}' target='_blank'>";
                if ($article->urlToImage) {
                    echo "<img src='{$article->urlToImage}' alt='Article Image'>";
                }
                echo "<div class='news-content'>";
                echo "<h4>{$article->title}</h4>";
                echo "<p>{$article->description}</p>";
                echo "</a>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p>Could not fetch news. Please check your API key or try again later.</p>";
        }
        ?>
    </div>
</div>

<style>
.news-container {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
}
.news-article {
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.news-article:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
.news-article a {
    text-decoration: none;
    color: #333;
    display: flex;
}
.news-article img {
    width: 200px;
    height: 100%;
    object-fit: cover;
}
.news-content {
    padding: 20px;
}
.news-content h4 {
    margin: 0 0 10px 0;
}
.news-content p {
    margin: 0;
    font-size: 14px;
}
</style>

<?php
// Include footer
include 'footer.php';
?>