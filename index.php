<?php
session_start();

// Set session timeout duration (in seconds)
$timeout_duration = 600; // 10 minutes

// Check if the "last_activity" session variable is set
if (isset($_SESSION['last_activity'])) {
    // Calculate the session's lifetime
    $elapsed_time = time() - $_SESSION['last_activity'];
    
    // If the elapsed time exceeds the timeout duration, destroy the session and redirect to login
    if ($elapsed_time > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: login.php?timeout=true");
        exit();
    }
}

// Update "last_activity" to the current time
$_SESSION['last_activity'] = time();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}
?>

<style>
    body, html {
        margin: 0;
        padding: 0;
        height: 100%;
        font-family: 'Exo', sans-serif;
        color: #fff;
        background-color: #2a2a2a; /* Dark background color */
    }

    .ui-container {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100vh; /* Full height */
        padding: 20px;
        box-sizing: border-box;
    }

    .top-section, .bottom-section {
        background: rgba(0, 0, 0, 0.8); /* Semi-transparent background with color */
        padding: 10px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0px 0px 20px 5px rgba(0,0,0,0.5);
    }

    .top-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .welcome-message {
        font-size: 1.5em;
        text-shadow: 0px 0px 10px #ff6f00;
    }

    .logout-button {
        padding: 10px;
        font-size: 1em;
        border: none;
        border-radius: 5px;
        background: linear-gradient(45deg, #ff6f00, #ff8e53);
        color: #fff;
        cursor: pointer;
        transition: 0.3s;
        box-shadow: 0px 0px 10px 2px #ff6f00;
    }

    .logout-button:hover {
        box-shadow: 0px 0px 20px 5px #ff8e53;
    }

    .choice-layer, .content-layer, .inventory-layer {
        padding: 20px;
        border-radius: 10px;
        background: rgba(42, 42, 42, 0.8);
        box-shadow: 0px 0px 20px 5px rgba(0,0,0,0.5);
    }

    .choice-layer {
        width: 20%; /* Adjust as needed */
        border-right: 2px solid #000;
        display: flex;
        flex-direction: column;
    }

    .content-layer {
        width: 60%; /* Adjust as needed */
        overflow-y: auto;
    }

    .inventory-layer {
        width: 20%; /* Adjust as needed */
        border-left: 2px solid #000;
        display: flex;
        flex-direction: column;
    }

    .advice-text {
        font-size: 1.2em;
        text-shadow: 0px 0px 10px #ff6f00;
        animation: fade 1s ease-in-out infinite;
    }

    .menu-title {
        padding: 10px;
        font-size: 1.5em;
        text-align: center;
        background-color: #333; /* You can change this to match your theme */
        border-radius: 5px;
        margin-bottom: 10px; /* Space between the title and the buttons */
        box-shadow: 0px 0px 10px 2px rgba(0,0,0,0.5);
        text-shadow: 0px 0px 10px #ff6f00;
    }

    .menu-button {
        width: 100%;
        padding: 15px;
        margin-bottom: 10px;
        font-size: 1.2em;
        color: #fff;
        background-color: #444; /* Button background color */
        border: none;
        border-radius: 5px;
        text-align: center;
        cursor: pointer;
        transition: background-color 0.3s ease;
        box-shadow: 0px 0px 10px 2px rgba(0,0,0,0.5);
    }

    .menu-button:hover {
        background-color: #555; /* Button hover effect */
    }
    
    @keyframes fade {
        0%, 100% { opacity: 0; }
        50% { opacity: 1; }
    }
    
</style>

<div class="ui-container">
    <div class="top-section">
        <div class="welcome-message">Welcome back, <?php echo $_SESSION['username']; ?>!</div>
        <form action="logout.php" method="post">
            <button type="submit" class="logout-button">Logout</button>
        </form>
    </div>

    <div class="main-content" style="display: flex; flex: 1; " >
        <div class="choice-layer">
            <div id="menu-title" class="menu-title">Main Menu</div>
            <button class="menu-button" onclick="changeMenu('Battle')">Battle</button>
            <button class="menu-button" onclick="changeMenu('User Stat')">User Stat</button>
            <button class="menu-button" onclick="changeMenu('Inventory')">Inventory</button>
        </div>

        <div class="content-layer" id="content-layer">
            <!-- Main game content goes here -->
            <p>You enter the dark forest. The trees seem to whisper around you. A goblin appears in the clearing ahead.</p>
        </div>
        
        <div class="inventory-layer">
            <!-- Player's inventory goes here -->
            <ul>
                <li>Sword (Equipped)</li>
                <li>Health Potion (x2)</li>
                <li>Magic Ring</li>
            </ul>
        </div>
    </div>

    <div class="bottom-section">
        <p class="advice-text" id="cyber-advice"></p>
    </div>
</div>

<script>
    function changeMenu(menuName) {
        document.getElementById('menu-title').textContent = menuName;
        // Change content based on the selected menu
        let content = '';
        if (menuName === 'Battle') {
            content = '<p>You prepare your weapon and get ready to fight.</p>';
        } else if (menuName === 'User Stat') {
            content = '<p>Your stats show your current level and abilities.</p>';
        } else if (menuName === 'Inventory') {
            content = '<p>You open your inventory and check your items.</p>';
        }
        document.getElementById('content-layer').innerHTML = content;
    }
    const advices = [
        "Keep your password updated.",
        "Use strong, unique passwords.",
        "Beware of phishing scams.",
        "Enable two-factor authentication.",
        "Regularly back up your data."
    ];
    let adviceIndex = 0;

    function showNextAdvice() {
        document.getElementById("cyber-advice").textContent = advices[adviceIndex];
        adviceIndex = (adviceIndex + 1) % advices.length;
    }

    // Show the first advice and then rotate every 10 seconds
    showNextAdvice();
    setInterval(showNextAdvice, 10000);
</script>
