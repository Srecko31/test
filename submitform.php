<?php
if(session_id() === "")
{
    session_start();
}
require_once "classes/bussinesLogic.php";
require_once "util.php";
require_once "classes/FormHelper.php";

$user = unserialize($_SESSION["user"]);

if($user[0]["rola"] == 1)
{
    require_once "nav.php";
}
?>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <title>Document</title>
<script>
    $(document).ready(function(){


    $("#register-form").submit(function(){
    var isFormValid = true;
    
//Input Validation
        $(".required").each(function(){
        var span = $("#" + "error" + $(this).attr("id"));
            
            if ($.trim($(this).val()).length == 0){
                $(this).addClass("highlight");
                span.text("Obavezno polje!");
                isFormValid = false;
            }
            else{
                $(this).removeClass("highlight");  
                span.text(""); 
            }
        });

//Radio Validation

        $(".requiredBox").each(function(){
            var name = this.name;
            var span = $("#" + "error" + $(this).attr("id"));

            if (!$('input[name='+ name +']:checked').length > 0) {   
                isFormValid = false;
                span.text("Obavezno polje!");
            }else{
                span.text(""); 
            }
        });

//Checkbox Validation
        $(".requiredCheckBox").each(function(){
            var className = this.className.split(' ')[1];
            
            var span = $("#" + "error" + $(this).attr("id"));
            
            if(!$("." + className + ":checkbox:checked").length > 0){ 
                isFormValid = false;
                span.text("Obavezno polje!");
            }else{
                span.text(""); 
            }
        });
    
    
    if (!isFormValid) alert("Please fill in all the required fields");
    return isFormValid;
});

    

    
    });
</script>
</head>
<body>

<?php
if(ISSET($_GET["id"]))
{
    $id = trim($_GET["id"]);
    

    $getQuestions = new bussinesLogic();
    $resultDB =  $getQuestions->getQuestions($id);
    if($resultDB["success"] && $resultDB["rows"] > 0)
    {
        fillForm($resultDB["data"],$user);
    }
}

    function fillForm($resultDB,$user)
    {
        echo "<h1>".$resultDB[1]["naziv"]."</h1>";
        $formHelper = new FormHelper("POST","submitdata.php");
        $formHelper->open_tag("register-form");
        $formHelper ->input("hidden","userID",$user[0]["userID"]);

        $pitanje = "";
        foreach($resultDB as $data)
        {
           
            if($pitanje != $data["pitanje"])
            {
                
                $pitanje = $data["pitanje"];
                echo $pitanje;
                ?>
                <span class ="errorClass" id="error<?php echo $data["questionsID"] ?>"></span>
                <?php
                echo "<br>";
            }
                     
            switch($data["typeID"])
            {   
                //name parametar
                // inpytype.questionid.answerid
                case Short_Answer:
                        $formHelper->input("text","text_".$data["questionsID"],"","required",$data["questionsID"]);
                    break;
                
                case Multiple_Choice:
                        $formHelper->input("checkbox","checkbox_".$data["questionsID"]."_".$data["answerID"],$data["odgovor"],"requiredCheckBox ". $data["questionsID"],$data["questionsID"]);
                        echo "<label>".$data["odgovor"]."</label>";
                    break;

                case One_Answer:
                        $formHelper->input("radio","radio_".$data["questionsID"],$data["odgovor"]."_".$data["answerID"],"requiredBox",$data["questionsID"]);
                        echo $data["odgovor"];
                        
                    break;
            }
            echo"<br><br>";
        }
        $formHelper->input("reset","reset","Reset");
        $formHelper->input("submit","submit","Submit");
        $formHelper->close_tag();
    }

    //postavim im id i stavim listener na njena na clikc ili sta god  i kroz ajax punim bazu
?>
</body>
