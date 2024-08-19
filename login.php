<style>
body, html {
    margin: 0;
    padding: 0;
    height: 100%;
    font-family: 'Exo', sans-serif;
    color: #fff;
    background-color: #2a2a2a; /* Dark background color */
}

.login-container {
    background-image: url('your-background-image.jpg');
    background-color: rgba(42, 42, 42, 0.8); /* Fallback color and slight overlay */
    background-blend-mode: overlay; /* Blend the color with the image */
    background-size: cover;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.login-box {
    background: rgba(0, 0, 0, 0.8); /* Semi-transparent background with color */
    padding: 40px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0px 0px 20px 5px rgba(0,0,0,0.5);
}

.game-title {
    font-size: 2.5em;
    margin-bottom: 20px;
    text-shadow: 0px 0px 10px #ff6f00;
}

.login-form .login-input {
    display: block;
    width: 100%;
    margin-bottom: 20px;
    padding: 10px;
    font-size: 1em;
    border: 2px solid #ff6f00;
    border-radius: 5px;
    background: #333;
    color: #fff;
    outline: none;
}

.login-button {
    width: 100%;
    padding: 10px;
    font-size: 1.2em;
    border: none;
    border-radius: 5px;
    background: linear-gradient(45deg, #ff6f00, #ff8e53);
    color: #fff;
    cursor: pointer;
    transition: 0.3s;
    box-shadow: 0px 0px 10px 2px #ff6f00;
}

.login-button:hover {
    box-shadow: 0px 0px 20px 5px #ff8e53;
}

.login-links a {
    color: #ff6f00;
    text-decoration: none;
    margin: 0 10px;
}

.login-links a:hover {
    text-decoration: underline;
}

</style>
<div class="login-container">
    <div class="login-box">
        <h1 class="game-title">Battle v1.0</h1>
        <form class="login-form">
            <input type="text" placeholder="Username" class="login-input">
            <input type="password" placeholder="Password" class="login-input">
            <button type="submit" class="login-button">Login</button>
        </form>
        <div class="login-links">
            <a href="#">Forgot Password?</a> | <a href="#">Create Account</a>
        </div>
    </div>
</div>
