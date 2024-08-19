<style>
    .ui-container {
    display: flex;
    height: 100vh; /* Full height */
}

.choice-layer {
    width: 20%; /* Adjust as needed */
    background-color: #ccc;
    padding: 10px;
    border-right: 2px solid #000;
    display: flex;
    flex-direction: column;
}

.content-layer {
    width: 60%; /* Adjust as needed */
    background-color: #f9f9f9;
    padding: 20px;
    overflow-y: auto;
}

.inventory-layer {
    width: 20%; /* Adjust as needed */
    background-color: #ddd;
    padding: 10px;
    border-left: 2px solid #000;
    display: flex;
    flex-direction: column;
}

</style>

<div class="ui-container">
    <div class="choice-layer">
        <!-- Player choices go here -->
        <button>Move to Forest</button>
        <button>Attack Goblin</button>
        <button>Talk to Merchant</button>
    </div>
    
    <div class="content-layer">
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
