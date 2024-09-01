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
            <button class="menu-button" id='battleBtn' onclick="changeMenu('Battle')">Battle</button>
            <button class="menu-button" id='userStatBtn'onclick="changeMenu('User Stat')">User Stat</button>
            <button class="menu-button" id='inventoryBtn'onclick="changeMenu('Inventory')">Inventory</button>
            <!-- Battle -->
            <button class="menu-button" id='easyBtn'onclick="changeMenu('Easy')" style="display: none;">Easy</button>
            <button class="menu-button" id='mediumBtn'onclick="changeMenu('Medium')" style="display: none;">Medium</button>
            <button class="menu-button" id='hardBtn'onclick="changeMenu('Hard')" style="display: none;">Hard</button>
            <button class="menu-button" id='InsaneBtn'onclick="changeMenu('Insane')" style="display: none;">Insane</button>
            
            <button class="menu-button" id='attackBtn'onclick="changeMenu('Attack')" style="display: none;">Attack</button>
            <button class="menu-button" id='defenseBtn'onclick="changeMenu('Defense')" style="display: none;">Defense</button>
            <button class="menu-button" id='quitBtn'onclick="changeMenu('Back')" style="display: none;">Quit to Menu</button>
            <!-- User Stat -->
            <!-- Inventory -->
            <button class="menu-button" id='sendItemBtn'onclick="changeMenu('Send Item')" style="display: none;">Send Item</button>
            <button class="menu-button" id='backBtn'onclick="changeMenu('Back')" style="display: none;">Back</button>
        </div>

        <div class="content-layer" id="content-layer">
            <!-- Main game content goes here -->
            <p>You enter the dark forest. The trees seem to whisper around you. A goblin appears in the clearing ahead.</p>
        </div>
        
        <div class="inventory-layer">
            <h2>Your Inventory</h2>
            <ul id="inventory-list">
                <!-- Inventory items will be inserted here by JavaScript -->
            </ul>   
        </div>
    </div>

    <div class="bottom-section">
        <p class="advice-text" id="cyber-advice"></p>
    </div>
</div>

<script>
    let easyMaxHp = 100;
    let easyhp = easyMaxHp;
    let easyattack = 5;
    let easydefense = 5;

    let mediumMaxHp = 150;
    let mediumhp = mediumMaxHp;
    let mediumattack = 10;
    let mediumdefense = 10;

    let hardMaxHp = 200;
    let hardhp = hardMaxHp;
    let hardattack = 15;
    let harddefense = 15;

    let insaneMaxHp = 300;
    let insanehp = insaneMaxHp;
    let insaneattack = 25;
    let insanedefense = 25;

    let enemyMaxHp;  // Maximum HP for easy level enemy
    let enemyHp;  // Current HP for easy level enemy
    let enemyAttack;
    let enemyDefense;
    
    let playerMaxHp = 100;  // Maximum HP for the player
    let playerHp = playerMaxHp;  // Current HP for the player
    let level = 5;
    let attack = 10;
    let defense = 10;
    let exp = 5;
    let expRequired = 100;

    function changeMenu(menuName) {

        console.log(easyhp);
        
        // Change content based on the selected menu
        let content = '';
        if (menuName === 'Battle') {
            document.getElementById('menu-title').textContent = menuName;
            document.getElementById('battleBtn').style.display = 'none';
            document.getElementById('userStatBtn').style.display = 'none';
            document.getElementById('inventoryBtn').style.display = 'none';
            
            document.getElementById('easyBtn').style.display = '';
            document.getElementById('mediumBtn').style.display = '';
            document.getElementById('hardBtn').style.display = '';
            document.getElementById('InsaneBtn').style.display = '';
            document.getElementById('backBtn').style.display = '';
            content = '<p>You prepare your weapon and get ready to fight.</p>';
        } else if (menuName === 'User Stat') {
            document.getElementById('menu-title').textContent = menuName;
            content = `<p>Your stats show your current level and abilities.</p>
                        <p>Level: ${level}</p>
                        <p>HP: ${hp}</p>
                        <p>Attack: ${attack}</p>
                        <p>Defense: ${defense}</p>
                        <p>Exp: ${exp} / ${expreqired}</p>`;
        } else if (menuName === 'Inventory') {
            document.getElementById('menu-title').textContent = menuName;
            document.getElementById('battleBtn').style.display = 'none';
            document.getElementById('userStatBtn').style.display = 'none';
            document.getElementById('inventoryBtn').style.display = 'none';
            
            document.getElementById('sendItemBtn').style.display = '';
            document.getElementById('backBtn').style.display = '';
            content = '<p>You open your inventory and check your items.</p>';
        } else if (menuName === 'Easy') {
            document.getElementById('menu-title').textContent = menuName;   

            enemyMaxHp = 100;
            enemyHp = enemyMaxHp;
            enemyAttack = 15;
            enemyDefense = 5;

            // Hide difficulty buttons and show action buttons
            battleUi();

            content = '<p>Enemy HP: ' + enemyHp + '</p>' +
                      '<p>Enemy Attack Power: ' + enemyAttack + '</p>' +
                      '<p>Enemy Defense Power: ' + enemyDefense + '</p>' +
                      '<p>Your HP: ' + playerHp + '</p>' +
                      '<p>Your Attack Power: ' + attack + '</p>' +
                      '<p>Your Attack Power: ' + defense + '</p>' +
                      '<p>Choose your action:</p>'
            //content = '<p>Your stats show your current level and abilities.</p>';
        } else if (menuName === 'Medium') {
            document.getElementById('menu-title').textContent = menuName;
            
            enemyMaxHp = 150;
            enemyHp = enemyMaxHp;
            enemyAttack = 25;
            enemyDefense = 10;

            battleUi();

            content = '<p>Enemy HP: ' + enemyHp + '</p>' +
                      '<p>Enemy Attack Power: ' + enemyAttack + '</p>' +
                      '<p>Enemy Defense Power: ' + enemyDefense + '</p>' +
                      '<p>Your HP: ' + playerHp + '</p>' +
                      '<p>Your Attack Power: ' + attack + '</p>' +
                      '<p>Your Attack Power: ' + defense + '</p>' +
                      '<p>Choose your action:</p>'
        } else if (menuName === 'Hard') {
            document.getElementById('menu-title').textContent = menuName;
            
            enemyMaxHp = 200;
            enemyHp = enemyMaxHp;
            enemyAttack = 40;
            enemyDefense = 15;

            battleUi();

            content = '<p>Enemy HP: ' + enemyHp + '</p>' +
                      '<p>Enemy Attack Power: ' + enemyAttack + '</p>' +
                      '<p>Enemy Defense Power: ' + enemyDefense + '</p>' +
                      '<p>Your HP: ' + playerHp + '</p>' +
                      '<p>Your Attack Power: ' + attack + '</p>' +
                      '<p>Your Attack Power: ' + defense + '</p>' +
                      '<p>Choose your action:</p>'
        } else if (menuName === 'Insane') {
            document.getElementById('menu-title').textContent = menuName;
            
            enemyMaxHp = 300;
            enemyHp = enemyMaxHp;
            enemyAttack = 60;
            enemyDefense = 40;

            battleUi();

            content = '<p>Enemy HP: ' + enemyHp + '</p>' +
                      '<p>Enemy Attack Power: ' + enemyAttack + '</p>' +
                      '<p>Enemy Defense Power: ' + enemyDefense + '</p>' +
                      '<p>Your HP: ' + playerHp + '</p>' +
                      '<p>Your Attack Power: ' + attack + '</p>' +
                      '<p>Your Attack Power: ' + defense + '</p>' +
                      '<p>Choose your action:</p>'
        } else if (menuName === 'Send Item') {
            document.getElementById('menu-title').textContent = menuName;
            content = '<p>Your stats show your current level and abilities.</p>';
        } else if (menuName === 'Back') {
            resetStats();

            document.getElementById('battleBtn').style.display = '';
            document.getElementById('userStatBtn').style.display = '';
            document.getElementById('inventoryBtn').style.display = '';
            
            document.getElementById('easyBtn').style.display = 'none';
            document.getElementById('mediumBtn').style.display = 'none';
            document.getElementById('hardBtn').style.display = 'none';
            document.getElementById('InsaneBtn').style.display = 'none';
            document.getElementById('attackBtn').style.display = 'none';
            document.getElementById('defenseBtn').style.display = 'none';
            document.getElementById('quitBtn').style.display = 'none';
            document.getElementById('backBtn').style.display = 'none';
            document.getElementById('sendItemBtn').style.display = 'none';
            
            content = '<p>Your stats show your current level and abilities.</p>';
            document.getElementById('menu-title').textContent = 'Main Menu';
        }else if (menuName === 'Attack') {
            // Player attacks
            let damageToEnemy = attack - enemyDefense;
            if (damageToEnemy < 0) damageToEnemy = 0;
            enemyHp -= damageToEnemy;

            content = '<p>You attacked the enemy!</p>' +
                      '<p>Enemy HP remaining: ' + enemyHp + '</p>';
            
            // Check if enemy is defeated
            if (enemyHp <= 0) {
                content += '<p>You defeated the enemy!</p>';
                document.getElementById('attackBtn').style.display = 'none';
                document.getElementById('defenseBtn').style.display = 'none';
            } else {
                // Enemy's turn to attack
                let missChance = Math.random();
                if (missChance > 0.1) {  // 90% chance to hit
                    let damageToPlayer = enemyAttack - defense;
                    if (damageToPlayer < 0) damageToPlayer = 0;
                    playerHp -= damageToPlayer;

                    content += '<p>The enemy attacks you!</p>' +
                               '<p>Your HP remaining: ' + playerHp + '</p>';
                } else {
                    content += '<p>The enemy missed their attack!</p>';
                }

                // Check if the player is defeated
                if (playerHp <= 0) {
                    content += '<p>You were defeated by the enemy!</p>';
                    document.getElementById('attackBtn').style.display = 'none';
                    document.getElementById('defenseBtn').style.display = 'none';
                }
            }
        }else if (menuName === 'Defense') {
            content = '<p>You prepare to defend!</p>';
        }
        document.getElementById('content-layer').innerHTML = content;
    }

    function resetStats() {
        playerHp = playerMaxHp;
        enemyHp = enemyMaxHp;
    }

    function battleUi(){
        document.getElementById('easyBtn').style.display = 'none';
        document.getElementById('mediumBtn').style.display = 'none';
        document.getElementById('hardBtn').style.display = 'none';
        document.getElementById('InsaneBtn').style.display = 'none';
        document.getElementById('backBtn').style.display = 'none';

        document.getElementById('attackBtn').style.display = '';
        document.getElementById('defenseBtn').style.display = '';
        document.getElementById('quitBtn').style.display = '';
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

    function loadInventory() {
        fetch('get_inventory.php')
            .then(response => response.json())
            .then(data => {
                const inventoryList = document.getElementById('inventory-list');
                inventoryList.innerHTML = ''; // Clear the list
            
                data.forEach(item => {
                    const li = document.createElement('li');
                    li.textContent = `${item.item_name} (x${item.quantity})`;
                    inventoryList.appendChild(li);
                });
            })
            .catch(error => console.error('Error fetching inventory:', error));
    }
    
    // Call this function when the inventory menu is opened
    loadInventory();
</script>
