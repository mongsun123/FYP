<?php
include('connection.php'); 
session_start();
$userId = $_SESSION['user_id'];
//var_dump($_SESSION);

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}
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

$query = "SELECT health, attack_power, defense, level, experience FROM character_stats WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($health, $attack_power, $defense, $level, $experience);
$stmt->fetch();
$stmt->close();
$expRequired = $level * 100;
// Update "last_activity" to the current time
$_SESSION['last_activity'] = time();

$email = '';
$stmt = $conn->prepare("SELECT username, password_hash, email, created_at FROM user WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($username, $password_hash, $email, $created_at);
$stmt->fetch();
$stmt->close();

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
    
    .inventory-table {
        width: 100%;
        max-width: 100%; /* Set a maximum width for all tables */
        margin-bottom: 20px;
        border-collapse: collapse;
    }

    .inventory-table th, .inventory-table td {
        padding: 8px;
        text-align: left;
        border: 1px solid #ddd;
    }

    @keyframes fade {
        0%, 100% { opacity: 0; }
        50% { opacity: 1; }
    }
    
</style>

<div class="ui-container">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <button class="menu-button" id='leaderboardBtn'onclick="changeMenu('Leaderboard')">Leaderboard</button>
            <button class="menu-button" id='profileBtn'onclick="changeMenu('Profile')">Profile</button>
            <!-- Battle -->
            <button class="menu-button" id='easyBtn'onclick="changeMenu('Easy')" style="display: none;">Easy</button>
            <button class="menu-button" id='mediumBtn'onclick="changeMenu('Medium')" style="display: none;">Medium</button>
            <button class="menu-button" id='hardBtn'onclick="changeMenu('Hard')" style="display: none;">Hard</button>
            <button class="menu-button" id='InsaneBtn'onclick="changeMenu('Insane')" style="display: none;">Insane</button>
            
            <button class="menu-button" id='attackBtn'onclick="changeMenu('Attack')" style="display: none;">Attack</button>
            <button class="menu-button" id='useItemBtn'onclick="changeMenu('Use Item')" style="display: none;">Use Item</button>
            <button class="menu-button" id='quitBtn'onclick="changeMenu('Back')" style="display: none;">Quit to Menu</button>
            <!-- User Stat -->
            <!-- Inventory -->
            <button class="menu-button" id='sendItemBtn'onclick="changeMenu('Send Item')" style="display: none;">Send Item</button>
            <button class="menu-button" id='backBtn'onclick="changeMenu('Back')" style="display: none;">Back</button>
        </div>

        <div class="content-layer" id="content-layer">
            <!-- Main game content goes here -->
            <!--<p>You enter the dark forest. The trees seem to whisper around you. A goblin appears in the clearing ahead.</p>-->
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
    const playerId = <?php echo json_encode($_SESSION['user_id']); ?>;

    let enemyMaxHp;  // Maximum HP for easy level enemy
    let enemyHp;  // Current HP for easy level enemy
    let enemyAttack;
    let enemyDefense;
    
    let playerMaxHp = <?php echo $health; ?>;
    let playerHp = playerMaxHp;  // Current HP for the player
    let attack = <?php echo $attack_power; ?>;
    let defense = <?php echo $defense; ?>;
    let level = <?php echo $level; ?>;
    let exp = <?php echo $experience; ?>;
    let expRequired = <?php echo $expRequired; ?>;
    
    let userId = <?php echo json_encode($userId); ?>;
    let username = <?php echo json_encode($username); ?>;
    let email = <?php echo json_encode($email); ?>;
    let password_hash = <?php echo json_encode($password_hash); ?>;
    let created_at = <?php echo json_encode($created_at); ?>;
    
    let temporaryAttack = attack; // Use temporary variable for attack power
    let temporaryHp = playerHp; // Use temporary variable for attack power
    let temporaryDefense = defense; // Use temporary variable for attack power
    let inventory = []; // Initialize an empty inventory

    function changeMenu(menuName) {
        // Change content based on the selected menu
        let content = '';
        if (menuName === 'Battle') {
            document.getElementById('menu-title').textContent = menuName;
            document.getElementById('battleBtn').style.display = 'none';
            document.getElementById('userStatBtn').style.display = 'none';
            document.getElementById('inventoryBtn').style.display = 'none';
            document.getElementById('leaderboardBtn').style.display = 'none';
            document.getElementById('profileBtn').style.display = 'none';
            
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
                        <p>HP: ${playerMaxHp}</p>
                        <p>Attack: ${attack}</p>
                        <p>Defense: ${defense}</p>
                        <p>Exp: ${exp} / ${expRequired}</p>`;
        } else if (menuName === 'Leaderboard') {
            document.getElementById('menu-title').textContent = menuName;
                
            // Fetch leaderboard data from the server
            fetch('get_leaderboard.php')
            .then(response => response.json())
            .then(data => {
                let content = '<h2>Leaderboard - Top 10 Players</h2>';
                
                if (data.length > 0) {
                    content += `<table class="inventory-table">
                                  <thead>
                                    <tr>
                                      <th style="width: 50%;">Username</th>
                                      <th style="width: 25%;">Level</th>
                                      <th style="width: 25%;">Experience</th>
                                    </tr>
                                  </thead>
                                  <tbody>`;
                    
                    // Add rows for each player
                    data.forEach(player => {
                        content += `<tr>
                                      <td style="width: 50%;">${player.username}</td>
                                      <td style="width: 25%;">${player.level}</td>
                                      <td style="width: 25%;">${player.experience}</td>
                                    </tr>`;
                    });
                    
                    content += `</tbody></table>`;
                } else {
                    content += '<p>No players found on the leaderboard.</p>';
                }
                
                document.getElementById('content-layer').innerHTML = content;
            })
            .catch(error => {
                console.error('Error fetching leaderboard data:', error);
                document.getElementById('content-layer').innerHTML = '<p>An error occurred while fetching the leaderboard. Please try again later.</p>';
            });
        } else if (menuName === 'Profile') {
            document.getElementById('menu-title').textContent = menuName;
            content = `<div id="profile-section">
                            <h2 id="menu-title">Profile</h2>
                            <form id="profile-form">
                                <label for="profile-id">ID: ${userId}</label><br>

                                <label for="profile-username">Username: </label>
                                <input type="text" id="profile-username" value="${username}" required><br>

                                <label for="profile-email">Email: ${email}</label><br>
                                <label for="profile-created-at">Created at: ${created_at}</label><br>

                                <label for="old-password">Old Password: </label>
                                <input type="password" id="old-password" required><br>

                                <label for="profile-password">New Password: </label>
                                <input type="password" id="profile-password" required><br>

                                <button type="button" id="save-profile">Save Changes</button>
                            </form>
                        </div>`;
        } else if (menuName === 'Inventory') {
            document.getElementById('menu-title').textContent = menuName;
            document.getElementById('battleBtn').style.display = 'none';
            document.getElementById('userStatBtn').style.display = 'none';
            document.getElementById('inventoryBtn').style.display = 'none';
            document.getElementById('leaderboardBtn').style.display = 'none';
            document.getElementById('profileBtn').style.display = 'none';
            
            document.getElementById('sendItemBtn').style.display = '';
            document.getElementById('backBtn').style.display = '';
            content = '<p>You open your inventory and check your items.</p>';

            // Fetch inventory data from the server
            fetch('get_inventory.php')
            .then(response => response.json())
            .then(data => {
                let content = '<h2>Your Inventory</h2>';
                let levels = {1: [], 2: [], 3: [], 4: []}; // To categorize items by levels
            
                // Categorize items by level
                data.forEach(item => {
                    if (levels[item.item_level]) {
                        levels[item.item_level].push(item);
                    }
                });
            
                // Construct the HTML content as a table
                for (let level in levels) {
                    if (levels[level].length > 0) {
                        content += `<h3>Level ${level} Items</h3>`;
                        content += `<table class="inventory-table">
                                      <thead>
                                        <tr>
                                          <th style="width: 20%;">Item Name</th>
                                          <th style="width: 40%;">Description</th>
                                          <th style="width: 10%;">Type</th>
                                          <th style="width: 10%;">Value</th>
                                          <th style="width: 10%;">Effect</th>
                                          <th style="width: 10%;">Quantity</th>
                                        </tr>
                                      </thead>
                                      <tbody>`;
                    
                        // Add rows for each item in the level
                        levels[level].forEach(item => {
                            content += `<tr>
                                          <td style="width: 20%;">${item.item_name}</td>
                                          <td style="width: 40%;">${item.item_description}</td>
                                          <td style="width: 10%;">${item.item_type}</td>
                                          <td style="width: 10%;">${item.item_value}</td>
                                          <td style="width: 10%;">${item.item_effect}</td>
                                          <td style="width: 10%;">${item.quantity}</td>
                                        </tr>`;
                        });
                    
                        content += `</tbody></table>`;
                    }
                }
            
                document.getElementById('content-layer').innerHTML = content; // Display the content as a table
            })
            .catch(error => {
                console.error('Error fetching inventory data:', error);
            });
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
                      '<p>Your Defense Power: ' + defense + '</p>' +
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
                      '<p>Your Defense Power: ' + defense + '</p>' +
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
                      '<p>Your Defense Power: ' + defense + '</p>' +
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
                      '<p>Your Defense Power: ' + defense + '</p>' +
                      '<p>Choose your action:</p>'
        } else if (menuName === 'Send Item') {
            document.getElementById('menu-title').textContent = menuName;
            content = `
                <p>Search for a user to send an item to:</p>
                <input type="text" id="search-input" placeholder="Enter username or user ID">
                <button onclick="searchUser()">Search</button>
                <div id="search-results"></div>
                <div id="send-item-form" style="display:none;">
                    <h3>Send Item</h3>
                    <input type="hidden" id="selected-user-id">
                    <div style="display: flex; align-items: center; margin-bottom: 20px;">
                        <input type="text" id="email" value="<?php echo $email; ?>" style="flex: 1; margin-right: 10px;" readonly required>
                        <button type="button" id="send-otp-button" class="login-button" style="width: auto; padding: 10px 10px;">Send OTP</button>
                    </div>
                    <div style="display: flex; align-items: center; margin-bottom: 20px;">
                        <input type="text" id="otp-code" placeholder="Enter Email OTP" class="login-input" style="flex: 1; margin-right: 10px;" required>
                        <input type="text" id="totp-code" placeholder="Enter TOTP" class="login-input" style="flex: 1;" required>
                    </div>
                    <label for="item-select">Choose an item to send:</label>
                    <select id="item-select"></select>
                    <button onclick="sendItem()">Send Item</button>
                </div>
            `;

            // Inject content into the DOM
            document.getElementById('content-layer').innerHTML = content;
        } else if (menuName === 'Back') {
            loadInventory();
            resetStats();

            document.getElementById('battleBtn').style.display = '';
            document.getElementById('userStatBtn').style.display = '';
            document.getElementById('inventoryBtn').style.display = '';
            document.getElementById('leaderboardBtn').style.display = '';
            document.getElementById('profileBtn').style.display = '';
            
            document.getElementById('easyBtn').style.display = 'none';
            document.getElementById('mediumBtn').style.display = 'none';
            document.getElementById('hardBtn').style.display = 'none';
            document.getElementById('InsaneBtn').style.display = 'none';
            document.getElementById('attackBtn').style.display = 'none';
            document.getElementById('useItemBtn').style.display = 'none';
            document.getElementById('quitBtn').style.display = 'none';
            document.getElementById('backBtn').style.display = 'none';
            document.getElementById('sendItemBtn').style.display = 'none';
            
            content = '<p>Back to Main Menu.</p>';
            document.getElementById('menu-title').textContent = 'Main Menu';
        }else if (menuName === 'Attack') {
            // Get player's level (this could be stored in a variable or retrieved from the database/session)
            let playerLevel;
            if(enemyMaxHp == 100){
                playerLevel = 1; // Example level, replace with actual level retrieval logic
            }else if(enemyMaxHp == 150){
                playerLevel = 2;
            }else if(enemyMaxHp == 200){
                playerLevel = 3;
            }else if(enemyMaxHp == 300){
                playerLevel = 4;
            }

            // Get items for the current level
            const itemsForLevel = getAllItemsByLevel(playerLevel);

            // Player attacks
            let damageToEnemy = temporaryAttack - enemyDefense;
            if (damageToEnemy < 0) damageToEnemy = 0;
            enemyHp -= damageToEnemy;

            content = '<p>You attacked the enemy!</p>' +
                      '<p>Enemy HP remaining: ' + enemyHp + '</p>';
            
            // Check if enemy is defeated
            if (enemyHp <= 0) {
                content += '<p>You defeated the enemy!</p>';
                document.getElementById('attackBtn').style.display = 'none';
                document.getElementById('useItemBtn').style.display = 'none';

                // Increase player EXP based on level
                let expGain = 0;
                switch (playerLevel) {
                    case 1:
                        expGain = 30;
                        break;
                    case 2:
                        expGain = 50;
                        break;
                    case 3:
                        expGain = 70;
                        break;
                    case 4:
                        expGain = 120;
                        break;
                }
            
                exp += expGain; // Add gained experience to player's total experience
                content += `<p>You gained ${expGain} experience points!</p>`;
                
                expDiff = expRequired - exp;
                if(expDiff <0 ){
                    expDiff = 0;
                }
                content += `<p>Still need  ${expDiff} experience to level up!</p>`;
                // Check if player levels up (example logic)
                if (exp >= expRequired) {
                    level++;
                    exp = exp - expRequired; // Reset experience points after leveling up
                    expRequired = level * 100; // Update the experience required for the next level
                    content += `<p>Congratulations! You've reached level ${level}!</p>`;
                    
                    playerMaxHp += 5;
                    attack += 2;
                    defense += 2;
                    content += `<p>Your stats have increased: HP +5, Attack +2, Defense +2</p>`;
                    
                }
                updatePlayerStats(exp, level, playerMaxHp, attack, defense);
                const selectedItem = getRandomItem(itemsForLevel);
                // Assign items to player based on the level
                content += '<p>You found the following item:</p>';
                content += `<p>Item: ${selectedItem}</p>`;

                addItemToInventory(playerId, selectedItem);
                loadInventory();
            } else {
                // Enemy's turn to attack
                let missChance = Math.random();
                if (missChance > 0.1) {  // 90% chance to hit
                    let damageToPlayer = enemyAttack - temporaryDefense;
                    if (damageToPlayer < 0) damageToPlayer = 0;
                    temporaryHp -= damageToPlayer;

                    content += '<p>The enemy attacks you!</p>' +
                               '<p>Your HP remaining: ' + temporaryHp + '</p>';
                } else {
                    content += '<p>The enemy missed their attack!</p>';
                }

                // Check if the player is defeated
                if (temporaryHp <= 0) {
                    content += '<p>You were defeated by the enemy!</p>';
                    document.getElementById('attackBtn').style.display = 'none';
                    document.getElementById('useItemBtn').style.display = 'none';
                }
            }
        }else if (menuName === 'Use Item') {
            // Fetch the player's inventory and filter for potions
            const potions = inventory.filter(item => item.item_type === 'potion'  && item.quantity > 0);

            if (potions.length === 0) {
                content = '<p>You have no potions available to use!</p>';
            } else {
                // Display available potions
                content = '<p>Select a potion to use:</p>';
                potions.forEach((potion, index) => {
                    content += `<button onclick="applyPotion(${index})">${potion.item_name} (${potion.item_description}) - Effect: ${potion.item_effect}</button><br>`;
                });
            }

            // Update the content area
            document.getElementById('content-layer').innerHTML = content;
            loadInventory();
        }
        document.getElementById('content-layer').innerHTML = content;
    }
    // Event listener for the Send OTP button using delegation
    document.addEventListener('click', function(event) {
        if (event.target && event.target.id === 'send-otp-button') {
            const emailInput = document.getElementById('email');
            const email = emailInput.value.trim();

            Swal.fire({
                title: 'Sending OTP...',
                html: 'Please wait while we send your OTP.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                willClose: () => {
                    Swal.hideLoading();
                }
            });

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'send_item_otp.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'OTP sent successfully!',
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to send OTP. Please try again.',
                        });
                    }
                }
            };

            xhr.onerror = function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again later.',
                });
            };

            xhr.send(`email=${encodeURIComponent(email)}`);
        }else if(event.target && event.target.id === 'save-profile'){
            // Get updated values from input fields
            const updatedUsername = document.getElementById('profile-username').value;
            const oldPassword = document.getElementById('old-password').value;
            const newPassword  = document.getElementById('profile-password').value;

            // Define the password regex (same as the one used in PHP)
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/;

            // Validate the password meets the criteria
            if (!passwordRegex.test(newPassword)) {
                alert('Password must be at least 8 characters long and include at least one lowercase letter, one uppercase letter, one digit, and one special character (!@#$%^&*).');
                return; // Stop the form submission if the password is invalid
            }
            // Create a data object to send to the server
            const profileData = {
                userId: userId, // Pass the userId you embedded from PHP
                username: updatedUsername,
                oldPassword: oldPassword,
                newPassword: newPassword
            };

            // Send an AJAX request to update the profile
            fetch('update_profile.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(profileData) // Convert JS object to JSON
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Profile updated successfully!');
                    location.reload();
                } else {
                    alert('Error updating profile: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });

    function resetStats() {
        playerHp = playerMaxHp;
        enemyHp = enemyMaxHp;
        
        temporaryAttack = attack;
        temporaryDefense = defense;
        temporaryHp = playerHp;
    }

    function getRandomItem(items) {
        return items[Math.floor(Math.random() * items.length)];
    }

    // Function to update player stats in the database
    function updatePlayerStats(exp, level, hp, attackPower, defense) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "update_experience.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    console.log("Experience and level updated successfully.");
                } else {
                    console.error("Failed to update experience and level:", response.message);
                }
            }
        };
        xhr.send(`exp=${exp}&level=${level}&hp=${hp}&attack_power=${attackPower}&defense=${defense}`);
    }

    function addItemToInventory(playerId, itemName) {
        // Example: Use AJAX to send data to a PHP script for inserting into the database
        fetch('add_item_to_inventory.php', {
            method: 'POST',
            body: JSON.stringify({ playerId, itemName }),
            headers: { 'Content-Type': 'application/json' }
        }).then(response => response.json())
        .then(data => {
            console.log(data.message);
            
            // After successfully adding the item to the database, update the inventory in the HTML
            if (data.success) {
                updateInventoryDisplay(itemName);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function updateInventoryDisplay(itemName) {
        const inventoryList = document.getElementById('inventory-list');
        const newItem = document.createElement('li');
        newItem.textContent = itemName;
        inventoryList.appendChild(newItem);
    }

    // Function to get items based on level
    function getAllItemsByLevel(level) {
        const items = {
            1: ["Wooden Sword", "Iron Sword", "Leather Armor", "Iron Armor", "Health Potion", "Attack Potion", "Defense Potion"],
            2: ["Steel Sword", "Golden Sword", "Chain Mail", "Steel Armor", "Greater Health Potion", "Greater Attack Potion", "Greater Defense Potion"],
            3: ["Dragon Sword", "Magic Sword", "Dragon Mail", "Magic Armor", "Superior Health Potion", "Superior Attack Potion", "Superior Defense Potion"],
            4: ["Excalibur", "Holy Sword", "Holy Armor", "Dragon Scale Armor", "Ultimate Health Potion", "Ultimate Attack Potion", "Ultimate Defense Potion"]
        };
        return items[level] || []; // Return items for the given level or an empty array if level is not matched
    }

    function applyPotion(index) {
        const potion = inventory.filter(item => item.item_type === 'potion')[index];
        let content = '';

        if (!potion) {
            content = '<p>Invalid potion selection.</p>';
        } else {
            // Apply the potion's effect
            if (potion.item_name.includes('Health Potion')) {
                temporaryHp += parseInt(potion.item_effect);
                content = `<p>You used a ${potion.item_name}. Your HP increased by ${potion.item_effect}!</p>
                            <p>Your HP: ${temporaryHp-potion.item_effect} -> ${temporaryHp}!</p>`;
            } else if (potion.item_name.includes('Attack Potion')) {
                temporaryAttack += parseInt(potion.item_effect);
                content = `<p>You used an ${potion.item_name}. Your attack increased by ${potion.item_effect}!</p>
                            <p>Your Attack Power: ${temporaryAttack-potion.item_effect} -> ${temporaryAttack}!</p>`;
            } else if (potion.item_name.includes('Defense Potion')) {
                temporaryDefense += parseInt(potion.item_effect);
                content = `<p>You used a ${potion.item_name}. Your defense increased by ${potion.item_effect}!</p>
                            <p>Your Defense Power: ${temporaryDefense-potion.item_effect} -> ${temporaryDefense}!</p>`;
            }

            // Update the inventory and database
            fetch('update_inventory.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ item_id: potion.item_id, user_id: playerId, quantity: potion.quantity - 1 })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadInventory(); // Reload the inventory after using an item
                } else {
                    content += `<p>Error updating inventory: ${data.message}</p>`;
                }
            })
            .catch(error => {
                console.error('Error updating inventory:', error);
                content += '<p>An error occurred. Please check the console for more details.</p>';
            });
        }

        document.getElementById('content-layer').innerHTML = content;
    }
    
    function searchUser() {
        const searchInput = document.getElementById('search-input').value;
        fetch('search_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ search: searchInput }),
        })
        .then(response => response.json())
        .then(users => {
            let resultsHtml = '<ul>';
            users.forEach(user => {
                resultsHtml += `<li>
                    <a style="color: white;" href="#" onclick="selectUser(${user.id}, '${user.username}')">${user.username} (ID: ${user.id})</a>
                </li>`;
            });
            resultsHtml += '</ul>';
            document.getElementById('search-results').innerHTML = resultsHtml;
        })
        .catch(error => {
            console.error('Error searching user:', error);
        });
    }
    
    function selectUser(userId, username) {
        document.getElementById('send-item-form').style.display = 'block';
        document.getElementById('selected-user-id').value = userId;
        document.getElementById('search-results').innerHTML = `<p>Selected User: ${username} (ID: ${userId})</p>`;
        populateItemSelect();
    }
    
    function populateItemSelect() {
        fetch('get_items.php')
        .then(response => response.json())
        .then(items => {
            const itemSelect = document.getElementById('item-select');
            itemSelect.innerHTML = '';
            items.forEach(item => {
                itemSelect.innerHTML += `<option value="${item.id}">${item.item_name}</option>`;
            });
        })
        .catch(error => {
            console.error('Error fetching items:', error);
        });
    }
    
    function sendItem() {
        const userId = document.getElementById('selected-user-id').value;
        const itemName = document.getElementById('item-select').value;
        const otp = document.getElementById('otp-code').value; // Add OTP input
        const totp = document.getElementById('totp-code').value; // Add TOTP input
        
        fetch('send_item.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ userId: userId, itemName: itemName,
            otp: otp,
            totp: totp }),
        })
        .then(response => response.json())
        .then(result => {
            Swal.fire({
                title: result.status === 'success' ? 'Success' : 'Error',
                text: result.message,
                icon: result.status === 'success' ? 'success' : 'error',
            }).then(() => {
                if (result.status === 'success') {
                    document.getElementById('send-item-form').style.display = 'none';
                    document.getElementById('search-results').innerHTML = '';
                }
            });
        })
        .catch(error => {
            console.error('Error sending item:', error);
        });
        loadInventory();
    }

    function battleUi(){
        document.getElementById('easyBtn').style.display = 'none';
        document.getElementById('mediumBtn').style.display = 'none';
        document.getElementById('hardBtn').style.display = 'none';
        document.getElementById('InsaneBtn').style.display = 'none';
        document.getElementById('backBtn').style.display = 'none';

        document.getElementById('attackBtn').style.display = '';
        document.getElementById('useItemBtn').style.display = '';
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
            inventory = data; // Store fetched inventory data globally
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
