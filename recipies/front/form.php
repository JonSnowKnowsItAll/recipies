<?php
if (isset($_POST['timerange']) or isset($_POST['searchMonth']))
{
    if (isset($_POST['timerange']))
    {
        try
        {
            $from = $_POST['timefrom'];
            $to = $_POST['timeto'];

            $timeRangeStmt = 'select rn.rez_name as Rezept, zb.rez_bereitgestellt_am as Datum
                                  from rezeptname rn, zubereitung zb
                                 where rn.rez_id = zb.rez_id
                                   and cast(zb.rez_bereitgestellt_am as date) 
                                between cast("'.$from.'" as date)
                                   and ifnull(cast("'.$to.'" as date), cast(now() as date))';
            showTable($timeRangeStmt);
        }
        catch(Exception $e)
        {
            echo 'Error - Rezeptsuche Datum - '.$e->getCode().': '.$e->getMessage();
        }
    }
    elseif (isset($_POST['searchMonth']))
    {
        try
        {
            if (isset($_POST['month']))
            {
                switch ($_POST['month'])
                {
                    case 'lastMonth':
                        $lastMontStmt = 'select rn.rez_name as Rezept, zb.rez_bereitgestellt_am as Datum
                                          from rezeptname rn, zubereitung zb
                                         where rn.rez_id = zb.rez_id
                                           and month(zb.rez_bereitgestellt_am) = month(now())-1
                                           and year(zb.rez_bereitgestellt_am) = year(now())';
                        showTable($lastMontStmt);
                        break;
                    case 'customMonth':
                        $chosenMonth = $_POST['chooseMonth'];
                        $customMonthstmt = 'select rn.rez_name as Rezept, zb.rez_bereitgestellt_am as Datum
                                              from rezeptname rn, zubereitung zb
                                             where rn.rez_id = zb.rez_id
                                               and month(zb.rez_bereitgestellt_am) = '.$chosenMonth.'
                                               and year(zb.rez_bereitgestellt_am) = year(now())';
                        showTable($customMonthstmt);
                         break;
                    case 'currentMonth':
                        $currentMonthStmt = 'select rn.rez_name as Rezept, zb.rez_bereitgestellt_am as Datum
                                              from rezeptname rn, zubereitung zb
                                             where rn.rez_id = zb.rez_id
                                               and month(zb.rez_bereitgestellt_am) = month(now())
                                               and year(zb.rez_bereitgestellt_am) = year(now())';
                        showTable($currentMonthStmt);
                        break;
                }
            }
        }
        catch(Exception $e)
        {
            echo 'Error - Rezeptsuche mit Monat - '.$e->getCode().': '.$e->getMessage();
        }
    }
}
else
{
    ?>
    <h1>Zeitraumsuche</h1>
    <h6>Rezepte nach Bereitstellungszeitraum durchsuchen</h6>
    <br>
    <form method="post">
        <label for="timefrom">Zeitraum von:</label>
        <input class="form-control" name="timefrom" type="date" required>
        <br>
        <label for="timeto">Zeitraum bis: </label>
        <input class="form-control" name="timeto" type="date">
        <br>
        <input class="btn btn-outline-success" type="submit" name="timerange" value="suche">
    </form>
    <hr>
    <h6>Oder wählen Sie aus folgenden Optionen</h6>
    <br>
    <form method="post">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="month" value="lastMonth">
            <label class="form-check-label" for="lastMonth">
                letzter Monat
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="month" value="currentMonth" checked>
            <label class="form-check-label" for="currentMonth">
                laufender Monat
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="month" value="customMonth">
            <input class="form-control" type="number" name="chooseMonth" min="1" max="12">
            <label class="form-check-label" for="customMonth">
                Monat des laufenden Jahres
            </label>
        </div>
        <br>
        <input class="btn btn-outline-warning" type="submit" name="searchMonth" value="suche">
    </form>
    <?php
}



