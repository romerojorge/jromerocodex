<?php




if ($_REQUEST["user_name"] == "slackbot") {
    exit;
}



$human_says = $_REQUEST["text"];



$bot_says = converse ($human_says);





if (is_null($bot_says) || trim($bot_says)==false) {
    $bot_says = "Sorry, I cannot understand you!";
}

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

function converse ($human) {
    $context = new ChatContext();
    $conIn = $context->getContext();
    $fname = null;
    $lname = null;




    switch ($conIn){
        case (is_null($conIn));
            $context ->setContext('fname?');

            return "hello welcome to onelessbyte, Lets start by setting up a profile, Please enter your first Name";

    break;
        case "fname";
            $context ->setContext('lname?');
            $fname = $human;
            return "hello" .$fname. " now enter your last name ";
        break;





    }


}
?>

