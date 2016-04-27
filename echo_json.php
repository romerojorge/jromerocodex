<?php
require_once ('phpFastCache/phpFastCache.php');
use phpFastCache\CacheManager;

$config = array(
    "storage"   =>  "Files",
    "path" => sys_get_temp_dir()
);
CacheManager::setup($config);

if ($_REQUEST["user_name"] == "slackbot") {
    exit;
}

$chat_id = $_REQUEST["token"] . "_" . $_REQUEST["user_name"];
$chat_history = CacheManager::get($chat_id . "_history");
if (is_null($chat_history)) {
    $chat_history = array ();
}
$chat_context = CacheManager::get($chat_id . "_context");
if (is_null($chat_context)) {
    $chat_context = array ();
}

$human_says = $_REQUEST["text"];
$chat_history = array_push ($chat_history, array("ts"=>time(), "text"=>$human_says));

$bot_says = converse ($human_says, $chat_context, $chat_history);
CacheManager::set($chat_id . "_context", $chat_context, 900); // cache for 600 seconds

if (is_null($bot_says) || trim($bot_says)==false) {
    $bot_says = "blah blah, please say something I actually understand!";
}
$chat_history = array_push ($chat_history, array("ts"=>time(), "text"=>$bot_says));
CacheManager::set($chat_id . "_history", $chat_history, 900); // cache for 600 seconds
?>
    {
    "text": "<?php echo htmlspecialchars($bot_says) ?>",
    "mrkdwn": true
    }
<?php
// INPUT PARAMS
//   $human is the human message to this bot
//   $context is an array that contains any data related to this chat session. You can put any data here and access it later.
//   $history is an array of the chat history.

// RETURNS the bot's next message to the human

function converse ($human, &$context, $history)
{


    if (is_null($context['status'])) {
        $context['status'] = "wait_for_name";
        return " WELCOME, Lets begin by setting up your ONELESSBYTE profile.\nPlease enter your First and Last Name";
    }

    // The user responded to the bot's question about name
    if ($context['status'] == "wait_for_name") {
        $context['name'] = $human;
        $context['status'] = "wait_for_height";
        return "Hello _" . $human . "_. please enter your Height in inches (ie 5ft = 60 inches.?";
    }

    // The user responded to the bot's question about birth year. We generate a response and set the status to NULL again so that we can start over.
    if ($context['status'] == "wait_for_height") {
        $context['height'] = $human;
        if ($human == 0) {
            return NULL;
        }
        $context['status'] = "wait_for_weight";
        return " thank you, I have recorded your height to be " . $human . ", Now please enter your weight";
    }
    if ($context['status'] == "wait_for_weight") {
        $context['weight'] = $human;
        if ($human == 0) {
            return NULL;

        }
        $context['status'] = "age?";
        return " I have recorded your weight to be " . $human . " please enter your age";


    }
    if ($context['status'] == "age?") {
        $context['age'] = $human;
        if ($human == 0) {
            return NULL;

        }
        $context['status'] = "sex?";
        return " I have recorded your age to be " . $human . " please enter your sex ( i.e male/female";


    }

    if ($context['status'] == "sex?") {
        $context['sex'] = $human;
        if ($human == "male") {
            $context['status'] = "maleBMR";
        }
        if ($human == "female") {
            $context['status'] = "femaleBMR";

        } elseif ($human == null) {
            return NULL;

        }

    }

    if ($context['status'] == "maleBMR") {

        // calculations before showing entire profile
        $BMR = 655 + (4.35 * floatval($context['weight'])) + (4.7 * floatval($context['height'])) - (4.7 * floatval($context['age']));
        $BMI = floatval((($context['weight'] * 703) / ($context ['height'] * $context['height'])));

        $context['bmi'] = $BMI;
        $context ['bmr'] = $BMR;
        if ($BMI <= 18.4) {
            $context['bmiStatus'] = "You are underweight, get some meat on those bones.";
        }
        if ($BMI >= 18.5 && $BMI <= 24.9) {
            $context['bmiStatus'] = "According to your BMI you are HEALTHY!!!";
        }
        if ($BMI >= 25.0 && $BMI <= 29.9) {
            $context['bmiStatus'] = "You are overweight, time to start eating healthier and exercising.";
        }
        if ($BMI >= 30.0) {
            $context['bmiStatus'] = "Your BMI says you are obese, a plan of action would be best for you.";
        }
        $context['status'] = "Activity?";
        return "What does your daily activity look like?"
        . "\n sedentary (little or no exercise at all)"
        . "\n light (exercise/sports 1-3 days a week)"
        . "\n moderate (exercise/sport 3-5 days a week)"
        . "\n heavy (exercise/sport 6-7 days a week)"
        . "\n hard (hard exercise/sport, physically demanding job, training)";

    }


    if ($context['status'] == "femaleBMR") {
        // calculations before showing entire profile
        $BMR = 655 + (4.35 * floatval($context['weight'])) + (4.7 * floatval($context['height'])) - (4.7 * floatval($context['age']));
        $BMI = floatval((($context['weight'] * 703) / ($context ['height'] * $context['height'])));

        $context['bmi'] = $BMI;
        $context ['bmr'] = $BMR;
        if ($BMI <= 18.4) {
            $context['bmiStatus'] = "You are underweight, get some meat on those bones.";
        }
        if ($BMI >= 18.5 && $BMI <= 24.9) {
            $context['bmiStatus'] = "According to your BMI you are HEALTHY!!!";
        }
        if ($BMI >= 25.0 && $BMI <= 29.9) {
            $context['bmiStatus'] = "You are overweight, time to start eating healthier and exercising.";
        }
        if ($BMI >= 30.0) {
            $context['bmiStatus'] = "Your BMI says you are obese, a plan of action would be best for you.";
        }
        $context['status'] = "Activity?";
        return "What does your daily activity look like?"
        . "\n sedentary (little or no exercise at all)"
        . "\n light (exercise/sports 1-3 days a week)"
        . "\n moderate (exercise/sport 3-5 days a week)"
        . "\n heavy (exercise/sport 6-7 days a week)"
        . "\n hard (hard exercise/sport, physically demanding job, training)";


    }
    if ($context['status'] == "Activity?") {
        $context['activity'] = $human;
        if ($human == "sedentary") {
            $context['bmr'] = 1.2 * $context['bmr'];
            $context['status'] = "profile";
        }
        if ($human == "light") {
            $context['bmr'] = 1.375 * $context['bmr'];
            $context['status'] = "profile";
        }
        if ($human == "moderate") {
            $context['bmr'] = 1.55 * $context['bmr'];
            $context['status'] = "profile";
        }
        if ($human == "heavy") {
            $context['bmr'] = 1.725 * $context['bmr'];
            $context['status'] = "profile";
        }
        if ($human == "hard") {
            $context['bmr'] = 1.9 * $context['bmr'];
            $context['status'] = "profile";
        } elseif ($human == null) {
            return NULL;
        }
    }

    if ($context['status'] == "profile") {
        $context['status'] = "NA";
        return "Name: " . $context['name'] . "\nHeight:" . $context['height']
        . "\nWeight: " . $context['weight']
        . "\nAge: " . $context['age']
        . "\nGender: " . $context['sex']
        . "\n BMI: " . $context['bmiStatus']
        . "\n BMR: "
        . "\n To lose weight you need: " . (round($context['bmr'], 0) - 500) . " cal daily" . "\n To keep your weight you need: " . round($context['bmr'], 0) . "  cal daily" . "\n To gain weight you need: " . (round($context['bmr'], 0) + 500) . " cal daily";
    }


// ****************************** conversation using buzz words words **************************************************
    if ($context['status'] == "NA") {
        if ((strpos($human, 'ate') !== false) or (strpos($human, 'had some') !== false)) {
            $context['status'] = "calorie_status";
            return "how many calories did you consume";
        }

        if ((strpos($human, 'workout') !== false) or (strpos($human, 'run') !== false)) {
            $context['status'] = "calorie_status_workout";
            return "how many calories did you burn?";
        }
        if ((strpos($human, 'status') !== false) or (strpos($human, 'progress') !== false)) {
            $context['status'] = "calorie_update";

        }

        if (strpos($human, 'bitch') !== false) {
            $context['status'] = "calorie_s";

        }
        if (strpos($human, 'profile') !== false) {
            $context['status'] = "profile_review";


        }



        if ((strpos($human, 'burn') !== false) and (strpos($human, '500') !== false)) {
            $context['status'] = "500calwork";


        }

        if ((strpos($human, 'burn') !== false) and (strpos($human, '300') !== false)) {
            $context['status'] = "300calwork";
        }

        if ((strpos($human, 'burn') !== false) and (strpos($human, '400') !== false)) {
            $context['status'] = "400calwork";
        }

        if ((strpos($human, 'fruit') !== false)) {
            $context['status'] = "fruitsnack";
        }

        if ((strpos($human, 'vegetable') !== false)) {
            $context['status'] = "vegetablesnack";
        }

        if ((strpos($human, 'meal') !== false)) {
            $context['status'] = "eatmeal";
        }

        if ((strpos($human, 'motivation') !== false)) {
            $context['status'] = "motivationn";
        }
        if ((strpos($human, 'What is BMI?') !== false)) {
            $context['status'] = "whatisbmi";
        }
        if ((strpos($human, 'favorite exercise') !== false)) {
            $context['status'] = "favexercise";
        }

        if ((strpos($human, 'lost') !== false)) {
            $context['status'] = "lostweight";
        }
        if ((strpos($human, 'sure') !== false)) {
            $context['status'] = "suure";
        }
        if ((strpos($human, 'change') !== false)) {
            $context['status'] = "UDProfile";
            return "What would you like to update on your profile?";
        }
        if ((strpos($human, 'seriously') !== false)) {
            $context['status'] = "update_weight";
            return " Okay, what is your new weight?";
        }





//### before this bracket


    }

    //*********************************** other conversation layouts ***************************************************
    if ($context['status'] == "calorie_status") {

        $context ['status'] = "NA";

        $context ['calories'] = $human;
        $context['calorie_counter'] += $context['calories'];

        if ($context ['calorie_counter'] >= $context['bmr']) {
            $context ['calorie_status'] = " Wow Slow down there buddy";
        } else {
            $context ['calorie_status'] = " ";
        }


        return " " . $context['calorie_status'] . "\n Your daily calorie balance is  " . $context['calorie_counter'] . " ";
    }
    if ($context['status'] == "UDProfile"){
        if ($human == "weight"){
            $context ['status'] = "update_weight";
            return " enter new weight in lb.";
        }
        if ($human == "age"){
            $context ['status'] = "update_age";
            return "Happy birthday, enter your new age";
        }elseif ($human == null) {
            return NULL;
        }



    }

    if ($context['status'] == "update_weight"){
        $context['status'] = 'NA';
        $context['weight'] = $human;

       // *********************************
        $BMR = 655 + (4.35 * floatval($context['weight'])) + (4.7 * floatval($context['height'])) - (4.7 * floatval($context['age']));
        $BMI = floatval((($context['weight'] * 703) / ($context ['height'] * $context['height'])));

        $context['bmi'] = $BMI;
        $context ['bmr'] = $BMR;
        if ($BMI <= 18.4) {
            $context['bmiStatus'] = "You are underweight, get some meat on those bones.";
        }
        if ($BMI >= 18.5 && $BMI <= 24.9) {
            $context['bmiStatus'] = "According to your BMI you are HEALTHY!!!";
        }
        if ($BMI >= 25.0 && $BMI <= 29.9) {
            $context['bmiStatus'] = "You are overweight, time to start eating healthier and exercising.";
        }
        if ($BMI >= 30.0) {
            $context['bmiStatus'] = "Your BMI says you are obese, a plan of action would be best for you.";
        }
        //**********************************

        return "Profile has been updated";



    }
    if ($context['status'] == "update_age"){
        $context['status'] = 'NA';
        $context['age'] = $human;

        // *********************************
        $BMR = 655 + (4.35 * floatval($context['weight'])) + (4.7 * floatval($context['height'])) - (4.7 * floatval($context['age']));
        $BMI = floatval((($context['weight'] * 703) / ($context ['height'] * $context['height'])));

        $context['bmi'] = $BMI;
        $context ['bmr'] = $BMR;
        if ($BMI <= 18.4) {
            $context['bmiStatus'] = "You are underweight, get some meat on those bones.";
        }
        if ($BMI >= 18.5 && $BMI <= 24.9) {
            $context['bmiStatus'] = "According to your BMI you are HEALTHY!!!";
        }
        if ($BMI >= 25.0 && $BMI <= 29.9) {
            $context['bmiStatus'] = "You are overweight, time to start eating healthier and exercising.";
        }
        if ($BMI >= 30.0) {
            $context['bmiStatus'] = "Your BMI says you are obese, a plan of action would be best for you.";
        }
        //**********************************

        return "Profile has been updated";



    }


    if ($context['status'] == "calorie_status_workout") {

        $context ['status'] = "NA";

        $context ['calories_burned'] = $human;
        $context['calorie_counter'] -= $context['calories_burned'];

        if ($context ['calorie_counter'] >= $context['bmr']) {
            $context ['calorie_status'] = " You need to work out more buddy";
        } else {
            $context ['calorie_status'] = " keep it up!";
        }


        return " " . $context['calorie_status'] . "\n Your daily calorie balance is  " . $context['calorie_counter'] . " ";
    }


    if ($context['status'] == "calorie_s") {
        $context ['status'] = "NA";
        return "fuck off";


    }

    if ($context['status'] == "500calwork") {

        $context ['status'] = "NA";
        return "Tennis (520 calories)\n
            Running (600 calories)\n
            Bicycling (600 calories)\n
            Football (600 calories)\n
            Basketball (600 calories)\n
            Soccer (600 calories)\n
            all 1 HOUR ";


    }

    if ($context['status'] == "profile_review") {
        $context['status'] = "NA";
        return "Name: " . $context['name'] . "\nHeight:" . $context['height']
        . "\nWeight: " . $context['weight']
        . "\nAge: " . $context['age']
        . "\nGender: " . $context['sex']
        . "\n BMI: " . $context['bmiStatus']
        . "\n BMR: "
        . "\n To lose weight you need: " . (round($context['bmr'], 0) - 500) . "cal daily" . "\n To keep your weight you need: " . round($context['bmr'], 0) . "cal daily" . "\n To gain weight you need: " . (round($context['bmr'], 0) + 500) . "cal daily";
    }


    //*****************fonz update**************************************************************************************


    if ($context['status'] == "300calwork") {
        $context ['status'] = "NA";
        return "Walk (300 calories)\n
			Kayaking (370 calories)\n
			Baseball (370 calories)\n
			Swimming (440 calories)\n
			Tennis (520 calories)\n
			Running (600 calories)\n
			Bicycling (600 calories)\n
			Football (600 calories)\n
			Basketball (600 calories)\n
			Soccer (600 calories)\n
			All 1 HOUR ";
    }

    if ($context['status'] == "400calwork") {
        $context ['status'] = "NA";
        return "Swimming (440 calories)\n
			Tennis (520 calories)\n
			Running (600 calories)\n
			Bicycling (600 calories)\n
			Football (600 calories)\n
			Basketball (600 calories)\n
			Soccer (600 calories)\n
			All in 1 HOUR ";
    }

    if ($context['status'] == "fruitsnack") {
        $context ['status'] = "NA";
        return "1 small Apple (80 calories)\n
			1 Banana (101 calories)\n
			1 Grape (2 calories)\n
			1 Mango (135 calories)\n
			1 Orange (71 calories)\n
			1 Pear (100 calories)\n
			1 cup of  Peach ( 38 calories)\n
			1 cup of Pineapple (80 calories)\n
			1 cup of Strawberry (53 calories)\n
			1 cup of Watermelon (45 calories)";
    }

    if ($context['status'] == "vegetablesnack") {
        $context ['status'] = "NA";
        return "1 cup  of Asparagus (36 calories)\n
			Bean curd (81 calories)\n
			1 cup  of Broccoli (40 calories)\n 
			1 cup  of Carrots (45 calories)\n
			1 Cucumber (30 calories)\n
			1 cup  of Eggplant (38 calories)\n
			1 cup  of Lettuce (7 calories)\n
			1 cup  of Tomato (29 calories)";
    }

    if ($context['status'] == "eatmeal") {
        $context ['status'] = "NA";
        return "1 cup  of Asparagus (36 calories)\n
			Bean curd (81 calories)\n
			1 cup  of Broccoli (40 calories)\n 
			1 cup  of Carrots (45 calories)\n
			1 Cucumber (30 calories)\n
			1 cup  of Eggplant (38 calories)\n
			1 cup  of Lettuce (7 calories)\n
			1 cup  of Tomato (29 calories)";
    }

    if ($context['status'] == "motivationn") {
        $context ['status'] = "NA";
        return "Do you want to see a shirtless pic of Ryan Reynolds? http://static.socialitelife.com/uploads/2011/01/ryan-reynolds-shirtless-photos-01192011-14-400x470.jpg";
    }


    if ($context['status'] == "favexercise") {
        $context ['status'] = "NA";
        return "1) Jumping to conclusion\n
				2) Carrying things too far\n 
				3) Pushing my luck";
    }
    if ($context['status'] == "whatisbmi") {
        $context ['status'] = "NA";
        return "As usual, too lazy to research anything yourself  (-__-)\nAnyways, you can use BMI to discover what your ideal weight should be for your height. For more info visit: http://www.thecalculatorsite.com/articles/health/what-is-body-mass-index.php";
    }

    if ($context['status'] == "calorie_update") {
        $context ['status'] = "NA";
        return " Calorie Status for today" . $context['calorie_counter'] . "\n To lose weight you need: " . (round($context['bmr'], 0) - 500) . "cal daily" . "\n To keep your weight you need: " . round($context['bmr'], 0) . "cal daily" . "\n To gain weight you need: " . (round($context['bmr'], 0) + 500) . "cal daily";
    }

    if ($context['status'] == "lostweight"){
        $context ['status'] = "NA";
        return "I have a picture that says otherwise. Do you want to see it?";
    }

    if ($context['status'] == "suure"){
        $context ['status'] = "NA";
        return "http://i.imgur.com/uv3whVy.jpg\nhttps://media.riffsy.com/images/9ddea6899165d002b3a0e77185698599/raw";
    }




    
}



?>