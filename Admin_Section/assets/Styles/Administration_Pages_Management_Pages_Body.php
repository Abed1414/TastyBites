<!DOCTYPE html>
<html>
<head>
<html lang="en">    
    <?php        
        $Page_Title = ""; 
        $Page_State = "";
        $Page_Priority = "";
        $Page_ID = $_GET['page_id'];
        $pageId = $Page_ID; 
        include('IDS_Academy_Administration_Styles/IDS_Academy_Administration_Applicants_Employees_Management_Style.php');
        include('../IDS_Academy_Connect.php'); 
        include('Retrive_Page_Info.php');
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <title><?php echo htmlspecialchars($Page_Title) . " Page Management"; ?></title>
</head>
    <script>
        function toggleInput(inputType) {
            var inputDiv = document.getElementById(inputType + "Input");
            if (inputDiv.style.display === "none") {
                inputDiv.style.display = "block";
            } else {
                inputDiv.style.display = "none";
            }
        }
    </script>
<body>
    <div class="container conatiner_costume_1" style = "margin-left: 10vw; margin-right: 0;">
        <div class="container conatiner_costume_2"><br><br>        
            <h1 style="margin-left: 26vw; margin-right; 30vw;"> <?php echo htmlspecialchars($Page_Title); ?> Page Content</h1><br><br>
                    <form class="form-inline" method="post" style="width: 600px; margin-left: 18.5vw; margin-right: 0;">
                            <div class="col" >
                                <br><br>
                                <div class="row-md-4">
                                    <h3>Page ID:</h3><br>
                                    <input type="number" class="form-control" id="title" disabled value="<?php echo htmlspecialchars($Page_ID) ?>" name="title">
                                </div><br>
                                <div class="row-md-4">
                                    <h3>Page Priority:</h3><br>
                                    <input type="number" class="form-control" id="priority" disabled value="<?php echo htmlspecialchars($Page_Priority) ?>" name="priority">
                                </div><br>
                                <div class="row-md-4">
                                    <h3>Page Title:</h3><br>
                                    <input type="text" class="form-control" id="title" value="<?php echo htmlspecialchars($Page_Title); ?>" name="title">
                                </div><br>
                                <div class="row-md-4">
                                    <h3>Page State:</h3><br>
                                    <input type="text" class="form-control" id="state" disabled value="<?php echo htmlspecialchars($Page_State) . " Page"; ?>" name="state">
                                </div><br><br>
                                <div class="row-md-4">
                                <button type="button" class="btn btn-angry custom-search-button-2 " onclick="toggleInput('state')" name="state" value="state"><h5 style = "margin-left: 7.6px;">Activate/Inactivate</h5></button>
                            </div>
                            <div id="stateInput" style="display: none;"><br><br>
                                <div class="custom-radio-group">
                                    <label class="custom-radio-inline">
                                        <input type="radio" class="form-check-input" id="newstate" name="newstate" value="Yes" style= "margin-right: 2px;"> <span style ="margin-right: 55px;">Yes</span>
                                    </label>
                                    <label class="custom-radio-inline">
                                        <input type="radio" class="form-check-input" id="newstateNo" name="newstateNo" value="No" style = "margin-right: 2px;"> No
                                    </label>
                                </div>
                            </div>
                        <br><br><br><br>
                        <h3>Body Sections:</h3><br><br>
                        <?php
                            include('IDS_Academy_Retrive_Page_Body_Sections.php');
                        ?>
                    <br><br>
                        <button type="submit" class="btn btn-angry custom-search-button-2" style="width: 180px; margin-left: 160px;" id="Update" name="Update" value="Update"><h4 style="margin-left: 8px;">Update</h4></button><br><br>
                            </div>
                        </form>
                    <br><br>
                </div>
            </div>      

            <?php
                    if($_SERVER["REQUEST_METHOD"] === "POST"){
                        $ID = $pageId;                    
                        if (isset($_POST['Update']) && $_POST['Update'] === 'Update') {
                                $new_title = $_POST['title']; 
                                $updateQuery = "UPDATE Pages SET Title = ? WHERE Page_ID = ?";
                                $stmt = mysqli_prepare($conn, $updateQuery);
                                mysqli_stmt_bind_param($stmt, "si", $new_title, $ID);
                                mysqli_stmt_execute($stmt);

                            if (isset($_POST["newstate"]) && $_POST["newstate"] === "Yes") {
                                $sql = "UPDATE Pages SET Active = 1 WHERE Page_ID = ?";
                                $stmtUpdateValidationEmail = $conn->prepare($sql);
                                $stmtUpdateValidationEmail->bind_param("i", $ID);
                                $stmtUpdateValidationEmail->execute();
                                $stmtUpdateValidationEmail->close();
                            }
                            if (isset($_POST["newstateNo"]) && $_POST["newstateNo"] === "No") {
                                $sql = "UPDATE Pages SET Active = 0 WHERE Page_ID = ?";
                                $stmtUpdateValidationEmail = $conn->prepare($sql);
                                $stmtUpdateValidationEmail->bind_param("i", $ID);
                                $stmtUpdateValidationEmail->execute();
                                $stmtUpdateValidationEmail->close();
                            }
                            
                            $i = 1;
                            while($i < $counter){
                                $bodyId = $i;
                                $bsid = "summernote" . $i;
                                $newBodySection = $_POST[$bsid];
                                $sqlUpdateBody = "UPDATE Pages_Body SET Body_Section = ? WHERE Page_ID = ? AND Body_Section_ID = ?";
                                $stmtUpdateBody = mysqli_prepare($conn, $sqlUpdateBody);
                                mysqli_stmt_bind_param($stmtUpdateBody, "sii", $newBodySection, $ID, $bodyId);
                                mysqli_stmt_execute($stmtUpdateBody);
                                mysqli_stmt_close($stmtUpdateBody);
                                $i++;
                            }
                        }
                            
                        
                    }
                ?>
        <br><br>
</body>
</html> 