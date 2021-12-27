<?php
if(session_id() === "")
{
    session_start();
}
require_once "db/dbconn.php";
require_once "util.php";

if(isset($_SESSION["user"]))
{
    $user = unserialize($_SESSION["user"]);
}else{
    //redirect to login
}


if($user[0]["rola"] == 1)
{
    require_once "nav.php";
      /*  echo"<a href='addForm.php'><img src='img/add.png' alt='add_img' width='50' height='50'>Dodaj novu formu</a>";*/
}


if(isset($_GET["id"]))
{
    $id = trim($_GET["id"]);

    $formData = getForma($id);

    var_dump($formData);
  
}
?>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen">
    <title>Document</title>

    <script>
        $(document).ready(function(){

            getSelect('selectType');


            var i =1;
            
            $("#addQuestion").click(function(){
                
                i++;
                $('#forma').append('<table id="tabela'+i+'"><tr id="question'+i+'"><td><input type="text" name="pitanja[]" class="questionInput" placeholder="Enter question here" required></td><td><select name="tip[]" id="selectType'+i+'" class="selectType" mandatory></select></td><td><button type="button" name="removeQ" id="'+i+'" class="removeQuestion" >X</button></td></tr><tr id="answers'+i+'" class="answerClass'+i+'"><td><input type="text" name="answers'+i+'[]" placeholder="Enter answer here" required></td><td><button type="button" id="'+i+'" name="add" class="addAnswer"  >Add More</button></td></tr></table>');
                getSelect('selectType'+i+'');
                
            });

            $(document).on('click', '.removeQuestion', function(){
                var button_id = $(this).attr("id");
                $('#tabela'+button_id).remove();
            });

            
            var count=0;
            $(document).on('click', '.addAnswer', function(){
                
                var x= this.id;
                
                count++;
                $('#tabela'+x).append('<tr id="answers'+count+'" class="answerClass'+x+'">  <td><input type="text" name="answers'+x+'[]" placeholder="Enter answer here" required></td><td><button type="button" name="removeA" id="'+count+'" class="removeAnswer" >X</button></td></tr>');
                
            });


            $(document).on('click', '.removeAnswer', function(){
                
                var button_id = $(this).attr("id");
                $('#answers'+button_id).remove();
            });

            


            $("#selectType").change(function(){
                
                if($(this).val() == 1)
                {
                    $("#answers").html("");
                }
            });

            
            $(document).on('change', '.selectType', function(){
                
                if(this.value == 1)
                {
                        if(this.id === 'selectType')
                        {   
                            $('.answerClass1').remove();
                        }else{
                        
                        var ID = this.id;
                        var removeID = ID.substr(-1);
                        $('.answerClass'+removeID).remove();
                        //$('#answers'+removeID).remove();
                    }
                }else{
                    
                    if(this.id === 'selectType')
                        {   
                            
                            $('.answerClass1').remove();
                            $('#tabela1').append('<tr id="answers" class="answerClass1">  <td><input type="text" name="answers[]" placeholder="Enter answer here" required></td><td><button type="button" class="addAnswer" name="add" id="1" >Add more</button></td></tr>');
                        }else{
                        
                        var ID = this.id;
                        var removeID = ID.substr(-1);
                        
                        $('.answerClass'+removeID).remove();
                        //$('#answers'+removeID).remove();
                        $('#tabela'+removeID).append('<tr id="answers" class="answerClass'+removeID+'">  <td><input type="text" name="answers'+removeID+'[]" placeholder="Enter answer here" required></td><td><button type="button" class="addAnswer" name="add" id="'+removeID+'" >Add more</button></td></tr>');
                        }
                    }
                    
            });
            


            $("#validateForm").submit(function(event){

                $(".selectType").each(function(){
                    requiredFields(this);
                });



            });
        });
           


        
        function requiredFields(obj)
        {
            if($(obj).val() == -1)
                    {
                        event.preventDefault();
                    }
        }


        function getSelect(id)
        {
            
            var req = "getTypes.php";

            $.getJSON(req,function(result){
                $('#'+id+'').append("<option value='-1'>------</option>");
                
                $.each(result, function(i, field){     
            
                    $('#'+id+'').append('<option value=' + field.ID + '>' + field.TYPE + '</option>');

                });
            });
        }

    </script>
</head>
<body>
    <form action="newForm.php" method="POST" id="validateForm">
        <div>
            <div>Naslov forme</div>
            <div>
                <input type="text" name="title" id="title" value = <?php echo $formData["naziv"][0] ?>  placeholder="Title" required>
            </div>
            <div>Opis forme</div>
            <div>
                <input type="text" name="desc" id="desc" value = <?php echo $formData["opis"][0] ?> placeholder="Description">
            </div>
        </div>
        <hr>
        <br><br>

        <div id="forma">
            <table id="tabela1">
                    <tr id="question">
                        <td><input type="text" name="pitanja[]" class="questionInput" placeholder="Enter question here" required></td>
                        <td><select name="tip[]" id="selectType" class="selectType">
                        </select></td>
                        <td><button type="button" name="add" id="addQuestion" class="newQuestion" >Add New Question</button></td>
                    </tr>
                    
                    <tr id="answers" class="answerClass1">  
                        <td><input type="text" name="answers[]" placeholder="Enter answer here" required></td>
                        <td><button type="button" class="addAnswer" name="add" id="1" >Add More</button></td>
                    </tr>
            </table>
            <?php
            $i=0;
            foreach($formData['data'] as $questionID=>$data)
            {
                $i++;
                echo '<table id="tabela'.$i.'"><tr id="question'.$i.'"><td><input type="text" name="pitanja[]" class="questionInput" value="'.$data['question'].'" placeholder="Enter question here" required></td><td><select name="tip[]" id="selectType'.$i.'" class="selectType" mandatory></select></td><td><button type="button" name="removeQ" id="'.$i.'" class="removeQuestion" >X</button></td></tr><tr id="answers'.$i.'" class="answerClass'.$i.'"><td><input type="text" name="answers'.$i.'[]" placeholder="Enter answer here" required></td><td><button type="button" id="'.$i.'" name="add" class="addAnswer"  >Add More</button></td></tr></table>';
                $count = 0;
                $x = $questionID;
                $type = $data["type"];
                foreach($data["answers"] as $answer)
                {
                    $count++;
                    if($type != 1)
                    {
                        echo '<tr id="answers'.$count.'" class="answerClass'.$x.'">  <td><input type="text"  name="answers'.$x.'[]" value = "'.$answer.'" placeholder="Enter answer here" required></td><td><button type="button" name="removeA" id="'.$count.'" class="removeAnswer" >X</button></td></tr>';
                    }
                }
            }

?>
        </div>
        <br><br><br>
        <input type="reset" name="reset" value ="Reset">
        <input type="submit" name="submit" value ="Submit">
    </form>
</body>
</html>






<?php

function getForma($id)
{
    $query= sprintf("SELECT f.naziv,
                            f.opis,
                            q.questionsID,
                            q.pitanje,
                            at.typeID,
                            at.tip,
                            a.answerID,
                            a.odgovor
                                FROM questions q
                                        LEFT JOIN answers a ON q.questionsID = a.questionsID
                                        JOIN forma f ON q.formaID = f.formaID
                                        JOIN answertype at ON q.typeID = at.typeID
                                WHERE q.formaID = %s",$id);

    $resultDB = DBConn::Select($query);

    if($resultDB["success"] && $resultDB["rows"] > 0)
    {
        $resultData = $resultDB["data"];
        $result = formData2Object($resultData);
    }

    return $result;
}

function formData2Object($resultData)
{
    $object["naziv"][] = $resultData[0]["naziv"];
    $object["opis"][] = $resultData[0]["opis"];
    
    foreach($resultData as $data)
    {
        $questionsID = $data["questionsID"];

        
            $object["data"][$questionsID]["question"] = $data["pitanje"];
            $object["data"][$questionsID]["type"] = $data["typeID"];
            $object["data"][$questionsID]["answers"][] = $data["odgovor"];
        
  
    }

    return $object;
}







?>