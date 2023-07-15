<h1>Nach Rezepten suchen</h1>
    <form method="post">
        <label for="search">Rezeptnamen suchen (auch Wortteil möglich):</label>
        <input class="form-control" type="text" name="search" id="search">
        <br>
        <input class="btn btn-outline-primary" type="submit" name="save" value="suchen">
        <hr>
    </form>
<?php
if (isset($_POST['save']) or isset($_POST['show']))
{
    try
    {
        echo '<h4>Gesucht wurde nach: <b>'.$_POST['search'].'</b></h4>';

        $recipe = '%'.$_POST['search'].'%';
        $searchstmt = 'select rez_id, rez_name as Rezept from rezeptname where lower(rez_name) like lower(?)';
        $valueArray = array($recipe);
        $stmt = makeStatement($searchstmt, $valueArray);

        if($count = $stmt->rowCount() == 0)
        {
            echo '<h6 style="color: tomato"><b>Keine Rezepte gefunden...</b></h6>';
        }
        else
        {
            ?>
            <form method="post">
                <?php
                echo '<br><label for="foundRecipes">Ergebnisliste:</label><select name="foundRecipes" class="form-control">';
                while($row=$stmt->fetch(PDO::FETCH_NUM))
                {
                    echo '<option value="'.$row[0].'">'.$row[1];
                }
                echo '</select><br>';
                ?>
                <br>
                <input hidden type="text" name="search" value=<?php echo $_POST['search']?>>
                <input class="btn btn-outline-info" type ="submit" name="show" value="anzeigen">
                <hr>
            </form>
            <?php
            if(isset($_POST['show']))
            {
                ?>
                <?php
                $recipeId = $_POST['foundRecipes'];
                $countStmt = 'select zub_id, zub_bez from zubereitung where rez_id = (?)';
                $valueArray = array($recipeId);
                $count = makeStatement($countStmt, $valueArray);

                while($row=$count->fetch(PDO::FETCH_NUM))
                {
                    echo 'Rezeptnummer '.$row[0].': '.$row[1];

                    $ingredientsStmt = 'select ze.zubein_menge as Menge, e.ein_name as Einheit, zu.zut_name as Zutat
                                      from zubereitung_einheit ze, zutat_einheit z, einheit e, zutat zu, zubereitung zub
                                     where ze.zuei_id = z.zuei_id
                                       and z.ein_id = e.ein_id
                                       and z.zut_id = zu.zut_id
                                       and ze.zub_id = zub.zub_id
                                       and zub.rez_id = '.$recipeId.'
                                       and zub.zub_id = '.$row[0];
                    showTable($ingredientsStmt);
                }
                echo '<hr>';
            }
        }
    }
    catch (Exception $e)
    {
        echo 'Error - Rezept suchen - '.$e->getCode().': '.$e->getMessage();
    }
}

