<?php

function fetchRecords($horseName)
{
	global $mysqli;
	$sql = 'SELECT 
    Horse,
    b.sire,
    b.dam,
    Color,
    Sex,
    Gait,
    a.Type,
    ET,
    Datefoal,
    Salecode,
    Price,
    Currency,
    Hip, 
    Day,
    Consno,
    Pemcode,
    Rating,
    Bredto,
    Lastbred,    
    Purlname,
    Purfname,
    Sbcity,
    Sbstate,
    Sbcountry
    FROM sales a
    JOIN damsire b ON a.damsire_Id=b.damsire_ID 
        WHERE a.chorse = "'.$horseName.'" order by a.datefoal DESC,a.saledate DESC';
	
	
	$result = mysqli_query($mysqli, $sql);
	if (!$result) {
	    printf("Errormessage: %s\n", $mysqli->error);
	}
	
	$json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
	
	return $json;
}

function fetchRecords_tb($horseName)
{
    global $mysqli;
    $sql = 'SELECT
    Horse,
    b.sire,
    b.dam,
    Color,
    Sex,
    a.Type,
    Datefoal,
    Salecode,
    Price,
    Currency,
    Hip,
    Day,
    Consno,
    Pemcode,
    Rating,
    Bredto,
    Lastbred,
    Purlname,
    Purfname,
    Sbcity,
    Sbstate,
    Sbcountry
    FROM tsales a
    JOIN tdamsire b ON a.damsire_Id=b.damsire_ID
        WHERE a.chorse = "'.$horseName.'" order by a.datefoal DESC,a.saledate DESC';
    
    
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    
    return $json;
}

function fetchOffsprings($damName)
{
    global $mysqli;
    $sql = 'SELECT
    Horse,
    b.sire,
    b.dam,
    Color,
    Sex,
    Gait,
    a.Type,
    ET,
    Datefoal,
    Salecode,
    Price,
    Currency,
    Hip,
    Day,
    Consno,
    Pemcode,
    Rating,
    Bredto,
    Lastbred,    
    Purlname,
    Purfname,
    Sbcity,
    Sbstate,
    Sbcountry
    FROM sales a
    JOIN damsire b ON a.damsire_Id=b.damsire_ID
    WHERE b.dam = "'.$damName.'" order by a.datefoal DESC,a.saledate DESC;';
    
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    
    if ($damName == "") {
        return "";
    }
    return $json;
}

function fetchOffsprings_broodmare($damName, $saleYear)
{
    global $mysqli;
    $sql = 'SELECT
    a.Horse,
    a.Hip,
    a.Sex,
    a.Salecode,
    a.Price,
    a.Rating
FROM sales a
JOIN damsire b ON a.damsire_Id = b.damsire_ID
JOIN sales c ON c.Horse = b.dam
WHERE b.dam = "'.$damName.'" AND c.type = "B"
AND c.lastbred <> "1900-01-01" 
AND a.datefoal >= DATE_ADD(c.lastbred, INTERVAL 11 MONTH)
AND a.datefoal <= DATE_ADD(c.lastbred, INTERVAL 13 MONTH)
AND YEAR(c.lastbred) >= '.$saleYear.'
AND a.type IN ("Y", "W")
ORDER BY a.datefoal DESC, a.saledate DESC
LIMIT 1;';
    
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    
    if ($damName == "") {
        return "";
    }
    return $json;
}

function fetchOffsprings_broodmare_tb($damName, $saleYear)
{
    global $mysqli;
    $sql = 'SELECT
    a.Horse,
    a.Hip,
    a.Sex,
    a.Salecode,
    a.Price,
    a.Rating
FROM tsales a
JOIN tdamsire b ON a.damsire_Id = b.damsire_ID
JOIN tsales c ON c.Horse = b.dam
WHERE b.dam = "'.$damName.'" AND c.type = "B"
AND c.lastbred <> "1900-01-01" 
AND a.datefoal >= DATE_ADD(c.lastbred, INTERVAL 11 MONTH)
AND a.datefoal <= DATE_ADD(c.lastbred, INTERVAL 13 MONTH)
AND YEAR(c.lastbred) >= '.$saleYear.'
AND a.type IN ("Y", "W")
ORDER BY a.datefoal DESC, a.saledate DESC
LIMIT 1;';
    
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    
    if ($damName == "") {
        return "";
    }
    return $json;
}

function fetchOffsprings_weanling_tb($damName)
{
    global $mysqli;
    
    // Validate input parameters
    if (empty($damName)) {
        return "";
    }

    // Prepare the SQL query with the required conditions
    $sql = '
    SELECT
    b.Horse,
    b.Hip,
    b.Sex,
    b.Salecode,
    b.Price,
    b.Rating,
    b.type AS b_type
    FROM tsales a
    JOIN tsales b ON a.TDAM = b.TDAM
    WHERE a.TDAM = "'.$damName.'"
    AND a.type = "W"
    AND a.yearfoal = b.yearfoal
    AND b.type = "Y";';

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    
    return $json;
}

function fetchOffsprings_tb($damName)
{
    global $mysqli;
    $sql = 'SELECT
    Horse,
    b.sire,
    b.dam,
    Color,
    Sex,
    a.Type,
    Datefoal,
    Salecode,
    Price,
    Currency,
    Hip,
    Day,
    Consno,
    Pemcode,
    Rating,
    Bredto,
    Lastbred,
    Purlname,
    Purfname,
    Sbcity,
    Sbstate,
    Sbcountry
    FROM tsales a
    JOIN tdamsire b ON a.damsire_Id=b.damsire_ID
    WHERE b.dam = "'.$damName.'" order by a.datefoal DESC,a.saledate DESC;';
    
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    
    if ($damName == "") {
        return "";
    }
    return $json;
}

function getHorseList()
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Horse FROM Sales';  // Select ONLY one, instead of all
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);

    return $json;
}

function getDamname($horseName)
{
    global $mysqli;
    $sql = 'SELECT
    b.dam
    FROM sales a
    JOIN damsire b ON a.damsire_Id=b.damsire_ID
    WHERE a.horse = "'.$horseName.'"';  // Select ONLY one, instead of all
    $result = $mysqli->query($sql);
    try {
        $row = $result->fetch_assoc();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    if ($row['dam'] == "") {
        $sql = 'SELECT
        b.damofdam as dam
        FROM sales a
        JOIN damsire b ON a.damsire_Id=b.damsire_ID
        WHERE b.dam = "'.$horseName.'"';  // Select ONLY one, instead of all
            $result = $mysqli->query($sql);
            try {
                $row = $result->fetch_assoc();
            } catch(PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            if (!$result) {
                printf("Errormessage: %s\n", $mysqli->error);
            }
    }
    return ($row['dam']);
}

function getDamname_tb($horseName)
{
    global $mysqli;
    $sql = 'SELECT
    b.dam
    FROM tsales a
    JOIN tdamsire b ON a.damsire_Id=b.damsire_ID
    WHERE a.horse = "'.$horseName.'"';  // Select ONLY one, instead of all
    $result = $mysqli->query($sql);
    try {
        $row = $result->fetch_assoc();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    if ($row['dam'] == "") {
        $sql = 'SELECT
        b.damofdam as dam
        FROM tsales a
        JOIN tdamsire b ON a.damsire_Id=b.damsire_ID
        WHERE b.dam = "'.$horseName.'"';  // Select ONLY one, instead of all
        $result = $mysqli->query($sql);
        try {
            $row = $result->fetch_assoc();
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        if (!$result) {
            printf("Errormessage: %s\n", $mysqli->error);
        }
    }
    return ($row['dam']);
}

function getTitleData($horseName)
{
    global $mysqli;
    $sql = 'SELECT
    b.sire,
    b.dam,
    b.damofdam
    FROM sales a
    JOIN damsire b ON a.damsire_Id=b.damsire_ID
    WHERE a.horse = "'.$horseName.'"';  // Select ONLY one, instead of all
    $result = $mysqli->query($sql);
    try {
        $row = $result->fetch_assoc();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    //return ($row[sire]." - ".$row[dam]." - ".$row[damofdam]);
    if ($row['dam'] == "") {
        $sql = 'SELECT distinct
        b.sireofdam,
        b.damofdam
        FROM sales a
        JOIN damsire b ON a.damsire_Id=b.damsire_ID
        WHERE b.dam = "'.$horseName.'"';  // Select ONLY one, instead of all
            $result = $mysqli->query($sql);
            try {
                $row = $result->fetch_assoc();
            } catch(PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            if (!$result) {
                printf("Errormessage: %s\n", $mysqli->error);
            }
    }
    return $row;
}

function getTitleData_tb($horseName)
{
    global $mysqli;
    $sql = 'SELECT
    b.sire,
    b.dam,
    b.damofdam
    FROM tsales a
    JOIN tdamsire b ON a.damsire_Id=b.damsire_ID
    WHERE a.horse = "'.$horseName.'"';  // Select ONLY one, instead of all
    $result = $mysqli->query($sql);
    try {
        $row = $result->fetch_assoc();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    //return ($row[sire]." - ".$row[dam]." - ".$row[damofdam]);
    if ($row['dam'] == "") {
        $sql = 'SELECT distinct
        b.sireofdam,
        b.damofdam
        FROM tsales a
        JOIN tdamsire b ON a.damsire_Id=b.damsire_ID
        WHERE b.dam = "'.$horseName.'"';  // Select ONLY one, instead of all
        $result = $mysqli->query($sql);
        try {
            $row = $result->fetch_assoc();
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        if (!$result) {
            printf("Errormessage: %s\n", $mysqli->error);
        }
    }
    return $row;
}

function getdamofdam($horseName)
{
    global $mysqli;
    $sql = 'SELECT
    b.dam
    FROM sales a
    JOIN damsire b ON a.damsire_Id=b.damsire_ID
    WHERE a.horse = "'.$horseName.'"';  // Select ONLY one, instead of all
    $result = $mysqli->query($sql);
    try {
        $row = $result->fetch_assoc();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    
    return $row['dam'];
}

function getdamofdam_tb($horseName)
{
    global $mysqli;
    $sql = 'SELECT
    b.dam
    FROM tsales a
    JOIN tdamsire b ON a.damsire_Id=b.damsire_ID
    WHERE a.horse = "'.$horseName.'"';  // Select ONLY one, instead of all
    $result = $mysqli->query($sql);
    try {
        $row = $result->fetch_assoc();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    
    return $row['dam'];
}


function fetchConsnoData($consno,$year,$elig,$gait,$sort1,$sort2,$sort3,$sort4,$sort5)
{
    global $mysqli;
    
    $searchParam = ' AND left(Consno,4)= IF("'.$consno.'"  = "", left(Consno,4), "'.$consno.'")
                     AND YEAR(Saledate)= IF("'.$year.'" = "", YEAR(Saledate), "'.$year.'")
                     AND Elig= IF("'.$elig.'"  = "", Elig, "'.$elig.'")
                     AND Gait= IF("'.$gait.'"  = "", Gait, "'.$gait.'")';
    
    $sql ='SELECT
            HIP,
            Horse,
            Sex,
            Color,
            Gait,
            a.Type,
            ET,
            Datefoal,
            Elig,
            b.Dam,
            Sireofdam,
            Salecode,
            Consno,
            Saledate,
            a.Day,
            Price,
            Currency,
            Purlname,
            Purfname,
            Rating
            FROM sales a
            JOIN damsire b ON a.damsire_Id=b.damsire_ID
            WHERE PRICE>0 '.$searchParam;
    
//     $join = ') a LEFT JOIN
//     (SELECT Price AS Rankprice ,(@curRank := @curRank + 1) AS Rank from (
//     SELECT Price FROM sales a
//     JOIN damsire b ON a.damsire_Id=b.damsire_ID WHERE TYPE= "Y" AND PRICE>0 ';
//     $join1 = 'LEFT JOIN
//     (select price  AS P1,sex AS S1,(@curRank1 := @curRank1 + 1) AS FRank from (
//              SELECT price, sex FROM sales a
//              JOIN damsire b ON a.damsire_Id=b.damsire_ID WHERE TYPE= "Y" AND Sex IN ("F","M") AND PRICE>0 ';
//     $join2 = 'LEFT JOIN
//     (select price  AS P2,sex AS S2,(@curRank2 := @curRank2 + 1) AS CRank from (
//              SELECT price, sex FROM sales a
//              JOIN damsire b ON a.damsire_Id=b.damsire_ID WHERE TYPE= "Y" AND Sex IN ("C","H","G") AND PRICE>0 ';
//     $searchConsno = ' AND Cosnno="'.$consno.'"';
//     $searchYear = ' AND YEAR(`SALEDATE`)="'.$year.'"';
//     $searchElig = ' AND Elig= "'.$elig.'" ';
//     $searchGait = ' AND Gait= "'.$gait.'" ';
    
    $orderby1 = ' ORDER BY '.$sort1;
    $orderby2 = ', '.$sort2;
    $orderby3 = ', '.$sort3;
    $orderby4 = ', '.$sort4;
    $orderby5 = ', '.$sort5;
    
    
//     $join11 = ' group by Price ORDER BY Price desc) as a,(SELECT @curRank := 0) r) b
//     on a.price=b.Rankprice '; //in order to do ranking becauserank function doesn't work on server.
//     $join21 = ' group by price,sex ORDER BY price desc) as a,(SELECT @curRank1 := 0) r) c
//              on a.price=c.P1 and a.Sex=c.S1 ';
//     $join31 = ' group by price,sex ORDER BY price desc) as a,(SELECT @curRank2 := 0) r) d
//              on a.price=d.P2 and a.Sex=d.S2 ';
    
//     if ($year != "" && $consno != "" && $elig != "" && $gait != "") {
//         $sql = $sql.$searchConsno.$searchElig.$searchYear.$searchGait.
//         $join.$searchConsno.$searchElig.$searchYear.$searchGait.$join11.
//         $join1.$searchConsno.$searchElig.$searchYear.$searchGait.$join21.
//         $join2.$searchConsno.$searchElig.$searchYear.$searchGait.$join31;
//     }elseif ($year != "" && $consno != "" && $elig != "") {
//         $sql = $sql.$searchConsno.$searchElig.$searchYear.
//         $join.$searchConsno.$searchElig.$searchYear.$join11.
//         $join1.$searchConsno.$searchElig.$searchYear.$join21.
//         $join2.$searchConsno.$searchElig.$searchYear.$join31;
//     }elseif ($year != "" && $consno != "" && $gait != "") {
//         $sql = $sql.$searchConsno.$searchGait.$searchYear.
//         $join.$searchConsno.$searchGait.$searchYear.$join11.
//         $join1.$searchConsno.$searchGait.$searchYear.$join21.
//         $join2.$searchConsno.$searchGait.$searchYear.$join31;
//     }elseif ($year != "" && $elig != "" && $gait != "") {
//         $sql = $sql.$searchElig.$searchGait.$searchYear.
//         $join.$searchElig.$searchGait.$searchYear.$join11.
//         $join1.$searchElig.$searchGait.$searchYear.$join21.
//         $join2.$searchElig.$searchGait.$searchYear.$join31;
//     }elseif ($consno != "" && $elig != "" && $gait != "") {
//         $sql = $sql.$searchConsno.$searchGait.$searchElig.
//         $join.$searchConsno.$searchGait.$searchElig.$join11.
//         $join1.$searchConsno.$searchGait.$searchElig.$join21.
//         $join2.$searchConsno.$searchGait.$searchElig.$join31;
//     }elseif ($year != "" && $consno != "") {
//         $sql = $sql.$searchConsno.$searchYear.$join.$searchConsno.$searchYear.$join11.
//         $join1.$searchConsno.$searchYear.$join21.$join2.$searchConsno.$searchYear.$join31;
//     }elseif ($consno != "" && $elig != "") {
//         $sql = $sql.$searchConsno.$searchElig.$join.$searchConsno.$searchElig.$join11.
//         $join1.$searchConsno.$searchElig.$join21.$join2.$searchConsno.$searchElig.$join31;
//     }elseif ($year != "" && $elig != "") {
//         $sql = $sql.$searchElig.$searchYear.$join.$searchElig.$searchYear.$join11.
//         $join1.$searchElig.$searchYear.$join21.$join2.$searchElig.$searchYear.$join31;
//     }elseif ($year != "" && $gait != "") {
//         $sql = $sql.$searchGait.$searchYear.$join.$searchGait.$searchYear.$join11.
//         $join1.$searchGait.$searchYear.$join21.$join2.$searchGait.$searchYear.$join31;
//     }elseif ($consno != "" && $gait != "") {
//         $sql = $sql.$searchGait.$searchConsno.$join.$searchGait.$searchConsno.$join11.
//         $join1.$searchGait.$searchConsno.$join21.$join2.$searchGait.$searchConsno.$join31;
//     }elseif ($elig != "" && $gait != "") {
//         $sql = $sql.$searchGait.$searchElig.$join.$searchGait.$searchElig.$join11.
//         $join1.$searchGait.$searchElig.$join21.$join2.$searchGait.$searchElig.$join31;
//     }elseif ($consno != "") {
//         $sql = $sql.$searchConsno.$join.$searchConsno.$join11.
//         $join1.$searchConsno.$join21.$join2.$searchConsno.$join31;
//     }elseif ($year != "") {
//         $sql = $sql.$searchYear.$join.$searchYear.$join11.$join1.$searchYear.$join21.$join2.$searchYear.$join31;
//     }elseif ($elig != "") {
//         $sql = $sql.$searchElig.$join.$searchElig.$join11.$join1.$searchElig.$join21.$join2.$searchElig.$join31;
//     }
    
    
    if ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !="" && $sort5 !="") {
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4.$orderby5;
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4;
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3;
    }elseif ($sort1 !="" && $sort2 !=""){
        $sql = $sql.$orderby1.$orderby2;
    }elseif ($sort1 !=""){
        $sql = $sql.$orderby1;
    }
    
    //echo $sql;
    $result = mysqli_query($mysqli, $sql);
    
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
        echo $sql;
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
    
    //Sample query above
    //     'SELECT * FROM (
    //         SELECT HIP, Horse, Sex, Color, Gait, A.Type, ET, Elig, B.Dam, Sireofdam, Salecode, Consno, Saledate, A.Day, Price, CONCAT (Purlname," " ,Purfname) As Buyer, Rating FROM Sales A JOIN Damsire B ON A.damsire_Id=B.damsire_ID WHERE TYPE= "Y" AND PRICE>0 AND B.Sire="A GO GO LAUXMONT"
    //         ) a left join
    //         (select price ,(@curRank := @curRank + 1) AS Ranking from (
        //             SELECT price FROM Sales A
        //             JOIN Damsire B ON A.damsire_Id=B.damsire_ID WHERE TYPE= "Y" AND PRICE>0
        //             AND B.Sire="A GO GO LAUXMONT" group by price ORDER BY price desc) as a,(SELECT @curRank := 0) r) b
    //             on a.price=b.price  ORDER BY A.SaleCode;'
}

function fetchSireData($sire,$year,$elig,$gait,$sort1,$sort2,$sort3,$sort4,$sort5)
{
    global $mysqli;
    $sql = 
    'SELECT `Rank`,`FRank`,`CRank`, HIP, Horse, Sex, Color,Gait, a.`Type`, ET, Datefoal, Elig, Dam, Sireofdam, Salecode, Consno, Saledate, `Day`, 
        a.Price, Currency, Purlname, Purfname, Rating FROM (
        SELECT        
        HIP,
        Horse,
        Sex,
        Color,
        Gait,
        a.`Type`,
        ET,
        Datefoal,
        Elig,
        b.Dam,
        Sireofdam,
        Salecode,
        Consno,
        Saledate,
        a.`Day`,
        Price,
        Currency,
        Purlname,
        Purfname,
        Rating
        FROM sales a
        JOIN damsire b ON a.damsire_Id=b.damsire_ID
        WHERE TYPE= "Y" AND PRICE>0 ';
    
    $join = ') a LEFT JOIN
    (SELECT Price AS Rankprice ,(@curRank := @curRank + 1) AS `Rank` from (
    SELECT Price FROM sales a 
    JOIN damsire b ON a.damsire_Id=b.damsire_ID WHERE TYPE= "Y" AND PRICE>0 ';
    $join1 = 'LEFT JOIN
    (select price  AS P1,sex AS S1,(@curRank1 := @curRank1 + 1) AS `FRank` from (
             SELECT price, sex FROM sales a
             JOIN damsire b ON a.damsire_Id=b.damsire_ID WHERE TYPE= "Y" AND Sex IN ("F","M") AND PRICE>0 ';
    $join2 = 'LEFT JOIN
    (select price  AS P2,sex AS S2,(@curRank2 := @curRank2 + 1) AS `CRank` from (
             SELECT price, sex FROM sales a
             JOIN damsire b ON a.damsire_Id=b.damsire_ID WHERE TYPE= "Y" AND Sex IN ("C","H","G") AND PRICE>0 ';
    $searchSire = ' AND b.Sire="'.$sire.'"';
    $searchYear = ' AND YEAR(`SALEDATE`)="'.$year.'"';
    $searchElig = ' AND Elig= "'.$elig.'" ';
    $searchGait = ' AND Gait= "'.$gait.'" ';
    
    $orderby1 = ' ORDER BY '.$sort1;
    $orderby2 = ', '.$sort2;
    $orderby3 = ', '.$sort3;
    $orderby4 = ', '.$sort4;
    $orderby5 = ', '.$sort5;

    $join11 = ' group by Price ORDER BY Price desc) as a,(SELECT @curRank := 0) r) b
    on a.price=b.Rankprice '; //in order to do ranking becauserank function doesn't work on server.
    $join21 = ' group by price,sex ORDER BY price desc) as a,(SELECT @curRank1 := 0) r) c
             on a.price=c.P1 and a.Sex=c.S1 '; 
    $join31 = ' group by price,sex ORDER BY price desc) as a,(SELECT @curRank2 := 0) r) d
             on a.price=d.P2 and a.Sex=d.S2 '; 
    
    if ($year != "" && $sire != "" && $elig != "" && $gait != "") {
        $sql = $sql.$searchSire.$searchElig.$searchYear.$searchGait.
        $join.$searchSire.$searchElig.$searchYear.$searchGait.$join11.
        $join1.$searchSire.$searchElig.$searchYear.$searchGait.$join21.
        $join2.$searchSire.$searchElig.$searchYear.$searchGait.$join31;
    }elseif ($year != "" && $sire != "" && $elig != "") {
        $sql = $sql.$searchSire.$searchElig.$searchYear.
        $join.$searchSire.$searchElig.$searchYear.$join11.
        $join1.$searchSire.$searchElig.$searchYear.$join21.
        $join2.$searchSire.$searchElig.$searchYear.$join31;
    }elseif ($year != "" && $sire != "" && $gait != "") {
        $sql = $sql.$searchSire.$searchGait.$searchYear.
        $join.$searchSire.$searchGait.$searchYear.$join11.
        $join1.$searchSire.$searchGait.$searchYear.$join21.
        $join2.$searchSire.$searchGait.$searchYear.$join31;
    }elseif ($year != "" && $elig != "" && $gait != "") {
        $sql = $sql.$searchElig.$searchGait.$searchYear.
        $join.$searchElig.$searchGait.$searchYear.$join11.
        $join1.$searchElig.$searchGait.$searchYear.$join21.
        $join2.$searchElig.$searchGait.$searchYear.$join31;
    }elseif ($sire != "" && $elig != "" && $gait != "") {
        $sql = $sql.$searchSire.$searchGait.$searchElig.
        $join.$searchSire.$searchGait.$searchElig.$join11.
        $join1.$searchSire.$searchGait.$searchElig.$join21.
        $join2.$searchSire.$searchGait.$searchElig.$join31;
    }elseif ($year != "" && $sire != "") {
        $sql = $sql.$searchSire.$searchYear.$join.$searchSire.$searchYear.$join11.
        $join1.$searchSire.$searchYear.$join21.$join2.$searchSire.$searchYear.$join31;
    }elseif ($sire != "" && $elig != "") {
        $sql = $sql.$searchSire.$searchElig.$join.$searchSire.$searchElig.$join11.
        $join1.$searchSire.$searchElig.$join21.$join2.$searchSire.$searchElig.$join31;
    }elseif ($year != "" && $elig != "") {
        $sql = $sql.$searchElig.$searchYear.$join.$searchElig.$searchYear.$join11.
        $join1.$searchElig.$searchYear.$join21.$join2.$searchElig.$searchYear.$join31;
    }elseif ($year != "" && $gait != "") {
        $sql = $sql.$searchGait.$searchYear.$join.$searchGait.$searchYear.$join11.
        $join1.$searchGait.$searchYear.$join21.$join2.$searchGait.$searchYear.$join31;
    }elseif ($sire != "" && $gait != "") {
        $sql = $sql.$searchGait.$searchSire.$join.$searchGait.$searchSire.$join11.
        $join1.$searchGait.$searchSire.$join21.$join2.$searchGait.$searchSire.$join31;
    }elseif ($elig != "" && $gait != "") {
        $sql = $sql.$searchGait.$searchElig.$join.$searchGait.$searchElig.$join11.
        $join1.$searchGait.$searchElig.$join21.$join2.$searchGait.$searchElig.$join31;
    }elseif ($sire != "") {
        $sql = $sql.$searchSire.$join.$searchSire.$join11.
        $join1.$searchSire.$join21.$join2.$searchSire.$join31;
    }elseif ($year != "") {
        $sql = $sql.$searchYear.$join.$searchYear.$join11.$join1.$searchYear.$join21.$join2.$searchYear.$join31;
    }elseif ($elig != "") {
        $sql = $sql.$searchElig.$join.$searchElig.$join11.$join1.$searchElig.$join21.$join2.$searchElig.$join31;
    }
    
    if ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !="" && $sort5 !="") {
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4.$orderby5." LIMIT 100;";
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4." LIMIT 100;";
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3." LIMIT 100;";
    }elseif ($sort1 !="" && $sort2 !=""){
        $sql = $sql.$orderby1.$orderby2." LIMIT 100;";
    }elseif ($sort1 !=""){
        $sql = $sql.$orderby1." LIMIT 100;";
    }
    
    //echo $sql;
    $result = mysqli_query($mysqli, $sql);
    
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
        echo $sql;
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
    
    //Sample query above
//     'SELECT * FROM (
//         SELECT HIP, Horse, Sex, Color, Gait, A.Type, ET, Elig, B.Dam, Sireofdam, Salecode, Consno, Saledate, A.Day, Price, CONCAT (Purlname," " ,Purfname) As Buyer, Rating FROM Sales A JOIN Damsire B ON A.damsire_Id=B.damsire_ID WHERE TYPE= "Y" AND PRICE>0 AND B.Sire="A GO GO LAUXMONT"
//         ) a left join
//         (select price ,(@curRank := @curRank + 1) AS Ranking from (
//             SELECT price FROM Sales A
//             JOIN Damsire B ON A.damsire_Id=B.damsire_ID WHERE TYPE= "Y" AND PRICE>0
//             AND B.Sire="A GO GO LAUXMONT" group by price ORDER BY price desc) as a,(SELECT @curRank := 0) r) b
//             on a.price=b.price  ORDER BY A.SaleCode;'
}

// function fetchSireData_tb($sire,$year,$elig,$gait,$sort1,$sort2,$sort3,$sort4,$sort5)
// {
//     global $mysqli;
    
//     $searchParam = ' AND YEAR(Saledate)= IF("'.$year.'" = "", YEAR(Saledate), "'.$year.'")
//                     AND b.Sire= IF("'.$sire.'"  = "", b.Sire, "'.$sire.'")
//                     AND Elig= IF("'.$elig.'"  = "", Elig, "'.$elig.'")
//                     AND Gait= IF("'.$gait.'"  = "", Gait, "'.$gait.'") ';
    
//     $sql =
//     'SELECT Rank,Frank,CRank, HIP, Horse, Sex, Color, `Type`, Datefoal, Elig, Dam, Sireofdam, Salecode, Consno, Saledate, `Day`, 
//         a.Price,Currency, Purlname, Purfname, Rating FROM (
//         SELECT
//         HIP,
//         Horse,
//         Sex,
//         Color,
//         a.Type,
//         Datefoal,
//         Elig,
//         b.Dam,
//         Sireofdam,
//         Salecode,
//         Consno,
//         Saledate,
//         a.Day,
//         Price,
//         Currency,
//         Purlname,
//         Purfname,
//         Rating
//         FROM tsales a
//         JOIN tdamsire b ON a.damsire_Id=b.damsire_ID
//         WHERE TYPE= "Y" AND PRICE>0';
    
//     $join = ') a LEFT JOIN
//     (SELECT Price AS Rankprice ,(@curRank := @curRank + 1) AS Rank from (
//     SELECT Price FROM tsales a
//     JOIN tdamsire b ON a.damsire_Id=b.damsire_ID WHERE TYPE= "Y" AND PRICE>0 ';
//     $join1 = ') LEFT JOIN
//     (select price  AS P1,sex AS S1,(@curRank1 := @curRank1 + 1) AS FRank from (
//             SELECT price, sex FROM tsales a
//             JOIN tdamsire b ON a.damsire_Id=b.damsire_ID WHERE TYPE= "Y" AND Sex IN ("F","M") AND PRICE>0 ';
//     $join2 = ') LEFT JOIN
//     (select price  AS P2,sex AS S2,(@curRank2 := @curRank2 + 1) AS CRank from (
//             SELECT price, sex FROM tsales a
//             JOIN tdamsire b ON a.damsire_Id=b.damsire_ID WHERE TYPE= "Y" AND Sex IN ("C","H","G") AND PRICE>0 ';
//     $searchSire = ' AND b.Sire="'.$sire.'"';
//     $searchYear = ' AND YEAR(`SALEDATE`)="'.$year.'"';
//     $searchElig = ' AND Elig= "'.$elig.'" ';
//     $searchGait = ' AND Gait= "'.$gait.'" ';
    
//     $orderby1 = ' ORDER BY '.$sort1;
//     $orderby2 = ', '.$sort2;
//     $orderby3 = ', '.$sort3;
//     $orderby4 = ', '.$sort4;
//     $orderby5 = ', '.$sort5;
    
    
//     $join11 = ' group by Price ORDER BY Price desc) as a,(SELECT @curRank := 0) r) b
//     on a.price=b.Rankprice '; //in order to do ranking becauserank function doesn't work on server.
//     $join21 = ' group by price,sex ORDER BY price desc) as a,(SELECT @curRank1 := 0) r) c
//             on a.price=c.P1 and a.Sex=c.S1 ';
//     $join31 = ' group by price,sex ORDER BY price desc) as a,(SELECT @curRank2 := 0) r) d
//             on a.price=d.P2 and a.Sex=d.S2 ';
    
//     if ($year != "" && $sire != "" && $elig != "" && $gait != "") {
//         $sql = $sql.$searchSire.$searchElig.$searchYear.$searchGait.
//         $join.$searchSire.$searchElig.$searchYear.$searchGait.$join11.
//         $join1.$searchSire.$searchElig.$searchYear.$searchGait.$join21.
//         $join2.$searchSire.$searchElig.$searchYear.$searchGait.$join31;
//     }elseif ($year != "" && $sire != "" && $elig != "") {
//         $sql = $sql.$searchSire.$searchElig.$searchYear.
//         $join.$searchSire.$searchElig.$searchYear.$join11.
//         $join1.$searchSire.$searchElig.$searchYear.$join21.
//         $join2.$searchSire.$searchElig.$searchYear.$join31;
//     }elseif ($year != "" && $sire != "" && $gait != "") {
//         $sql = $sql.$searchSire.$searchGait.$searchYear.
//         $join.$searchSire.$searchGait.$searchYear.$join11.
//         $join1.$searchSire.$searchGait.$searchYear.$join21.
//         $join2.$searchSire.$searchGait.$searchYear.$join31;
//     }elseif ($year != "" && $elig != "" && $gait != "") {
//         $sql = $sql.$searchElig.$searchGait.$searchYear.
//         $join.$searchElig.$searchGait.$searchYear.$join11.
//         $join1.$searchElig.$searchGait.$searchYear.$join21.
//         $join2.$searchElig.$searchGait.$searchYear.$join31;
//     }elseif ($sire != "" && $elig != "" && $gait != "") {
//         $sql = $sql.$searchSire.$searchGait.$searchElig.
//         $join.$searchSire.$searchGait.$searchElig.$join11.
//         $join1.$searchSire.$searchGait.$searchElig.$join21.
//         $join2.$searchSire.$searchGait.$searchElig.$join31;
//     }elseif ($year != "" && $sire != "") {
//         $sql = $sql.$searchSire.$searchYear.$join.$searchSire.$searchYear.$join11.
//         $join1.$searchSire.$searchYear.$join21.$join2.$searchSire.$searchYear.$join31;
//     }elseif ($sire != "" && $elig != "") {
//         $sql = $sql.$searchSire.$searchElig.$join.$searchSire.$searchElig.$join11.
//         $join1.$searchSire.$searchElig.$join21.$join2.$searchSire.$searchElig.$join31;
//     }elseif ($year != "" && $elig != "") {
//         $sql = $sql.$searchElig.$searchYear.$join.$searchElig.$searchYear.$join11.
//         $join1.$searchElig.$searchYear.$join21.$join2.$searchElig.$searchYear.$join31;
//     }elseif ($year != "" && $gait != "") {
//         $sql = $sql.$searchGait.$searchYear.$join.$searchGait.$searchYear.$join11.
//         $join1.$searchGait.$searchYear.$join21.$join2.$searchGait.$searchYear.$join31;
//     }elseif ($sire != "" && $gait != "") {
//         $sql = $sql.$searchGait.$searchSire.$join.$searchGait.$searchSire.$join11.
//         $join1.$searchGait.$searchSire.$join21.$join2.$searchGait.$searchSire.$join31;
//     }elseif ($elig != "" && $gait != "") {
//         $sql = $sql.$searchGait.$searchElig.$join.$searchGait.$searchElig.$join11.
//         $join1.$searchGait.$searchElig.$join21.$join2.$searchGait.$searchElig.$join31;
//     }elseif ($sire != "") {
//         $sql = $sql.$searchSire.$join.$searchSire.$join11.
//         $join1.$searchSire.$join21.$join2.$searchSire.$join31;
//     }elseif ($year != "") {
//         $sql = $sql.$searchYear.$join.$searchYear.$join11.$join1.$searchYear.$join21.$join2.$searchYear.$join31;
//     }elseif ($elig != "") {
//         $sql = $sql.$searchElig.$join.$searchElig.$join11.$join1.$searchElig.$join21.$join2.$searchElig.$join31;
//     }
    
    
//     if ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !="" && $sort5 !="") {
//         $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4.$orderby5;
//     }elseif ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !=""){
//         $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4;
//     }elseif ($sort1 !="" && $sort2 !="" && $sort3 !=""){
//         $sql = $sql.$orderby1.$orderby2.$orderby3;
//     }elseif ($sort1 !="" && $sort2 !=""){
//         $sql = $sql.$orderby1.$orderby2;
//     }elseif ($sort1 !=""){
//         $sql = $sql.$orderby1;
//     }
    
//     echo "Generated SQL Query: " . $sql;
//     //echo $sql;
//     $result = mysqli_query($mysqli, $sql);
    
//     if (!$result) {
//         printf("Errormessage: %s\n", $mysqli->error);
//         echo $sql;
//     }
//     $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
//     return $json;
    
//     //Sample query above
//     //     'SELECT * FROM (
//     //         SELECT HIP, Horse, Sex, Color, Gait, A.Type, ET, Elig, B.Dam, Sireofdam, Salecode, Consno, Saledate, A.Day, Price, CONCAT (Purlname," " ,Purfname) As Buyer, Rating FROM Sales A JOIN Damsire B ON A.damsire_Id=B.damsire_ID WHERE TYPE= "Y" AND PRICE>0 AND B.Sire="A GO GO LAUXMONT"
//     //         ) a left join
//     //         (select price ,(@curRank := @curRank + 1) AS Ranking from (
//         //             SELECT price FROM Sales A
//         //             JOIN Damsire B ON A.damsire_Id=B.damsire_ID WHERE TYPE= "Y" AND PRICE>0
//         //             AND B.Sire="A GO GO LAUXMONT" group by price ORDER BY price desc) as a,(SELECT @curRank := 0) r) b
//     //             on a.price=b.price  ORDER BY A.SaleCode;'
// }

// function fetchSireData_tb($sire, $year, $elig, $sort1, $sort2, $sort3, $sort4, $sort5)
// {
//     global $mysqli;
    
//     $searchParam = ' AND YEAR(Saledate)= IF("'.$year.'" = "", YEAR(Saledate), "'.$year.'")
//                     AND b.Sire= IF("'.$sire.'"  = "", b.Sire, "'.$sire.'")
//                     AND Elig= IF("'.$elig.'"  = "", Elig, "'.$elig.'")';
    
//     $sql =
//     'SELECT Rank,FRank,CRank, HIP, Horse, Sex, Color, `Type`, Datefoal, Elig, Dam, Sireofdam, Salecode, Consno, Saledate, `Day`, 
//         a.Price,Currency, Purlname, Purfname, Rating FROM (
//         SELECT
//         HIP,
//         Horse,
//         Sex,
//         Color,
//         a.Type,
//         Datefoal,
//         Elig,
//         b.Dam,
//         Sireofdam,
//         Salecode,
//         Consno,
//         Saledate,
//         a.Day,
//         Price,
//         Currency,
//         Purlname,
//         Purfname,
//         Rating
//         FROM tsales a
//         JOIN tdamsire b ON a.damsire_Id=b.damsire_ID
//         WHERE TYPE= "Y" AND PRICE>0) a';
    
//     $join = ' LEFT JOIN
//     (SELECT Price AS Rankprice ,(@curRank := @curRank + 1) AS Rank from (
//     SELECT Price FROM tsales a
//     JOIN tdamsire b ON a.damsire_Id=b.damsire_ID WHERE TYPE= "Y" AND PRICE>0';
//     $join1 = ' LEFT JOIN
//     (select price  AS P1,sex AS S1,(@curRank1 := @curRank1 + 1) AS FRank from (
//             SELECT price, sex FROM tsales a
//             JOIN tdamsire b ON a.damsire_Id=b.damsire_ID WHERE TYPE= "Y" AND Sex IN ("F","M") AND PRICE>0 ';
//     $join2 = ' LEFT JOIN
//     (select price  AS P2,sex AS S2,(@curRank2 := @curRank2 + 1) AS CRank from (
//             SELECT price, sex FROM tsales a
//             JOIN tdamsire b ON a.damsire_Id=b.damsire_ID WHERE TYPE= "Y" AND Sex IN ("C","H","G") AND PRICE>0';
   
//     $searchSire = ' AND b.Sire="'.$sire.'"';
//     $searchYear = ' AND YEAR(`SALEDATE`)="'.$year.'"';
//     $searchElig = ' AND Elig= "'.$elig.'" ';
    
//     $join11 = ' group by Price ORDER BY Price desc) as a,(SELECT @curRank := 0) r) b
//     on a.price=b.Rankprice '; //in order to do ranking becauserank function doesn't work on server.
//     $join21 = ' group by price,sex ORDER BY price desc) as a,(SELECT @curRank1 := 0) r) c
//             on a.price=c.P1 and a.Sex=c.S1 ';
//     $join31 = ' group by price,sex ORDER BY price desc) as a,(SELECT @curRank2 := 0) r) d
//             on a.price=d.P2 and a.Sex=d.S2; ';
    
//     if ($year != "" && $sire != "" && $elig != "") {
//         $sql = $sql.$searchSire.$searchElig.$searchYear.
//         $join.$searchSire.$searchElig.$searchYear.$join11.
//         $join1.$searchSire.$searchElig.$searchYear.$join21.
//         $join2.$searchSire.$searchElig.$searchYear.$join31;
//     } elseif ($year != "" && $sire != "") {
//         $sql = $sql.$searchSire.$searchYear.$join.$searchSire.$searchYear.$join11.
//         $join1.$searchSire.$searchYear.$join21.$join2.$searchSire.$searchYear.$join31;
//     } elseif ($sire != "" && $elig != "") {
//         $sql = $sql.$searchSire.$searchElig.$join.$searchSire.$searchElig.$join11.
//         $join1.$searchSire.$searchElig.$join21.$join2.$searchSire.$searchElig.$join31;
//     } elseif ($year != "" && $elig != "") {
//         $sql = $sql.$searchElig.$searchYear.$join.$searchElig.$searchYear.$join11.
//         $join1.$searchElig.$searchYear.$join21.$join2.$searchElig.$searchYear.$join31;
//     } elseif ($sire != "") {
//         $sql = $sql.$searchSire.$join.$searchSire.$join11.
//         $join1.$searchSire.$join21.$join2.$searchSire.$join31;
//     } elseif ($year != "") {
//         $sql = $sql.$searchYear.$join.$searchYear.$join11.$join1.$searchYear.$join21.$join2.$searchYear.$join31;
//     } elseif ($elig != "") {
//         $sql = $sql.$searchElig.$join.$searchElig.$join11.$join1.$searchElig.$join21.$join2.$searchElig.$join31;
//     }
    
//     $orderby1 = ' ORDER BY '.$sort1;
//     $orderby2 = ', '.$sort2;
//     $orderby3 = ', '.$sort3;
//     $orderby4 = ', '.$sort4;
//     $orderby5 = ', '.$sort5;
    
//     if ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !="" && $sort5 !="") {
//         $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4.$orderby5;
//     }elseif ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !=""){
//         $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4;
//     }elseif ($sort1 !="" && $sort2 !="" && $sort3 !=""){
//         $sql = $sql.$orderby1.$orderby2.$orderby3;
//     }elseif ($sort1 !="" && $sort2 !=""){
//         $sql = $sql.$orderby1.$orderby2;
//     }elseif ($sort1 !=""){
//         $sql = $sql.$orderby1;
//     }
    
//     echo "Generated SQL Query: " . $sql;
//     //echo $sql;
//     $result = mysqli_query($mysqli, $sql);
    
//     if (!$result) {
//         printf("Errormessage: %s\n", $mysqli->error);
//         echo $sql;
//     }
//     $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
//     return $json;
    
//     //Sample query above
//     //     'SELECT * FROM (
//     //         SELECT HIP, Horse, Sex, Color, Gait, A.Type, ET, Elig, B.Dam, Sireofdam, Salecode, Consno, Saledate, A.Day, Price, CONCAT (Purlname," " ,Purfname) As Buyer, Rating FROM Sales A JOIN Damsire B ON A.damsire_Id=B.damsire_ID WHERE TYPE= "Y" AND PRICE>0 AND B.Sire="A GO GO LAUXMONT"
//     //         ) a left join
//     //         (select price ,(@curRank := @curRank + 1) AS Ranking from (
//         //             SELECT price FROM Sales A
//         //             JOIN Damsire B ON A.damsire_Id=B.damsire_ID WHERE TYPE= "Y" AND PRICE>0
//         //             AND B.Sire="A GO GO LAUXMONT" group by price ORDER BY price desc) as a,(SELECT @curRank := 0) r) b
//     //             on a.price=b.price  ORDER BY A.SaleCode;'
// }

function fetchSireData_tb($sire,$year,$elig,$gait,$sort1,$sort2,$sort3,$sort4,$sort5)
{
    global $mysqli;
    $sql = 
    'SELECT `Rank`,`FRank`,`CRank`, HIP, Horse, Sex, Color, a.`Type`, Datefoal, Elig, Dam, Sireofdam, Salecode, Consno, Saledate, `Day`, 
        a.Price, Currency, Purlname, Purfname, Rating FROM (
        SELECT        
        HIP,
        Horse,
        Sex,
        Color,
        a.`Type`,
        Datefoal,
        Elig,
        b.Dam,
        Sireofdam,
        Salecode,
        Consno,
        Saledate,
        a.`Day`,
        Price,
        Currency,
        Purlname,
        Purfname,
        Rating
        FROM tsales a
        JOIN tdamsire b ON a.damsire_Id=b.damsire_ID
        WHERE TYPE= "Y" AND PRICE>0 ';
    
    $join = ') a LEFT JOIN
    (SELECT Price AS Rankprice ,(@curRank := @curRank + 1) AS `Rank` from (
    SELECT Price FROM tsales a 
    JOIN tdamsire b ON a.damsire_Id=b.damsire_ID WHERE TYPE= "Y" AND PRICE>0 ';
    $join1 = 'LEFT JOIN
    (select price  AS P1,sex AS S1,(@curRank1 := @curRank1 + 1) AS `FRank` from (
             SELECT price, sex FROM tsales a
             JOIN tdamsire b ON a.damsire_Id=b.damsire_ID WHERE TYPE= "Y" AND Sex IN ("F","M") AND PRICE>0 ';
    $join2 = 'LEFT JOIN
    (select price  AS P2,sex AS S2,(@curRank2 := @curRank2 + 1) AS `CRank` from (
             SELECT price, sex FROM tsales a
             JOIN tdamsire b ON a.damsire_Id=b.damsire_ID WHERE TYPE= "Y" AND Sex IN ("C","H","G") AND PRICE>0 ';
    $searchSire = ' AND b.Sire="'.$sire.'"';
    $searchYear = ' AND YEAR(`SALEDATE`)="'.$year.'"';
    $searchElig = ' AND Elig= "'.$elig.'" ';
    $searchGait = ' AND Gait= "'.$gait.'" ';
    
    $orderby1 = ' ORDER BY '.$sort1;
    $orderby2 = ', '.$sort2;
    $orderby3 = ', '.$sort3;
    $orderby4 = ', '.$sort4;
    $orderby5 = ', '.$sort5;
   
    $join11 = ' group by Price ORDER BY Price desc) as a,(SELECT @curRank := 0) r) b
    on a.price=b.Rankprice '; //in order to do ranking becauserank function doesn't work on server.
    $join21 = ' group by price,sex ORDER BY price desc) as a,(SELECT @curRank1 := 0) r) c
             on a.price=c.P1 and a.Sex=c.S1 '; 
    $join31 = ' group by price,sex ORDER BY price desc) as a,(SELECT @curRank2 := 0) r) d
             on a.price=d.P2 and a.Sex=d.S2 '; 
    
    if ($year != "" && $sire != "" && $elig != "" && $gait != "") {
        $sql = $sql.$searchSire.$searchElig.$searchYear.$searchGait.
        $join.$searchSire.$searchElig.$searchYear.$searchGait.$join11.
        $join1.$searchSire.$searchElig.$searchYear.$searchGait.$join21.
        $join2.$searchSire.$searchElig.$searchYear.$searchGait.$join31;
    }elseif ($year != "" && $sire != "" && $elig != "") {
        $sql = $sql.$searchSire.$searchElig.$searchYear.
        $join.$searchSire.$searchElig.$searchYear.$join11.
        $join1.$searchSire.$searchElig.$searchYear.$join21.
        $join2.$searchSire.$searchElig.$searchYear.$join31;
    }elseif ($year != "" && $sire != "" && $gait != "") {
        $sql = $sql.$searchSire.$searchGait.$searchYear.
        $join.$searchSire.$searchGait.$searchYear.$join11.
        $join1.$searchSire.$searchGait.$searchYear.$join21.
        $join2.$searchSire.$searchGait.$searchYear.$join31;
    }elseif ($year != "" && $elig != "" && $gait != "") {
        $sql = $sql.$searchElig.$searchGait.$searchYear.
        $join.$searchElig.$searchGait.$searchYear.$join11.
        $join1.$searchElig.$searchGait.$searchYear.$join21.
        $join2.$searchElig.$searchGait.$searchYear.$join31;
    }elseif ($sire != "" && $elig != "" && $gait != "") {
        $sql = $sql.$searchSire.$searchGait.$searchElig.
        $join.$searchSire.$searchGait.$searchElig.$join11.
        $join1.$searchSire.$searchGait.$searchElig.$join21.
        $join2.$searchSire.$searchGait.$searchElig.$join31;
    }elseif ($year != "" && $sire != "") {
        $sql = $sql.$searchSire.$searchYear.$join.$searchSire.$searchYear.$join11.
        $join1.$searchSire.$searchYear.$join21.$join2.$searchSire.$searchYear.$join31;
    }elseif ($sire != "" && $elig != "") {
        $sql = $sql.$searchSire.$searchElig.$join.$searchSire.$searchElig.$join11.
        $join1.$searchSire.$searchElig.$join21.$join2.$searchSire.$searchElig.$join31;
    }elseif ($year != "" && $elig != "") {
        $sql = $sql.$searchElig.$searchYear.$join.$searchElig.$searchYear.$join11.
        $join1.$searchElig.$searchYear.$join21.$join2.$searchElig.$searchYear.$join31;
    }elseif ($year != "" && $gait != "") {
        $sql = $sql.$searchGait.$searchYear.$join.$searchGait.$searchYear.$join11.
        $join1.$searchGait.$searchYear.$join21.$join2.$searchGait.$searchYear.$join31;
    }elseif ($sire != "" && $gait != "") {
        $sql = $sql.$searchGait.$searchSire.$join.$searchGait.$searchSire.$join11.
        $join1.$searchGait.$searchSire.$join21.$join2.$searchGait.$searchSire.$join31;
    }elseif ($elig != "" && $gait != "") {
        $sql = $sql.$searchGait.$searchElig.$join.$searchGait.$searchElig.$join11.
        $join1.$searchGait.$searchElig.$join21.$join2.$searchGait.$searchElig.$join31;
    }elseif ($sire != "") {
        $sql = $sql.$searchSire.$join.$searchSire.$join11.
        $join1.$searchSire.$join21.$join2.$searchSire.$join31;
    }elseif ($year != "") {
        $sql = $sql.$searchYear.$join.$searchYear.$join11.$join1.$searchYear.$join21.$join2.$searchYear.$join31;
    }elseif ($elig != "") {
        $sql = $sql.$searchElig.$join.$searchElig.$join11.$join1.$searchElig.$join21.$join2.$searchElig.$join31;
    }
    
    if ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !="" && $sort5 !="") {
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4.$orderby5." LIMIT 100;";
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4." LIMIT 100;";
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3." LIMIT 100;";
    }elseif ($sort1 !="" && $sort2 !=""){
        $sql = $sql.$orderby1.$orderby2." LIMIT 100;";
    }elseif ($sort1 !=""){
        $sql = $sql.$orderby1." LIMIT 100;";
    }

    //echo $sql;
    $result = mysqli_query($mysqli, $sql);
    
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
        echo $sql;
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
    
    //Sample query above
//     'SELECT * FROM (
//         SELECT HIP, Horse, Sex, Color, Gait, A.Type, ET, Elig, B.Dam, Sireofdam, Salecode, Consno, Saledate, A.Day, Price, CONCAT (Purlname," " ,Purfname) As Buyer, Rating FROM Sales A JOIN Damsire B ON A.damsire_Id=B.damsire_ID WHERE TYPE= "Y" AND PRICE>0 AND B.Sire="A GO GO LAUXMONT"
//         ) a left join
//         (select price ,(@curRank := @curRank + 1) AS Ranking from (
//             SELECT price FROM Sales A
//             JOIN Damsire B ON A.damsire_Id=B.damsire_ID WHERE TYPE= "Y" AND PRICE>0
//             AND B.Sire="A GO GO LAUXMONT" group by price ORDER BY price desc) as a,(SELECT @curRank := 0) r) b
//             on a.price=b.price  ORDER BY A.SaleCode;'
}

function fetchConsAnalysis($consno,$year,$elig,$gait)
{
    global $mysqli;
    $sql = 'SELECT * FROM cons_sales_allyear';
    
    
    if ($year != "" && $consno != "" && $elig != "" && $gait != "") {
        $sql = 'SELECT * FROM cons_sales_elig WHERE Consno ="'.$consno.'" AND Year = '.$year.' AND
                Elig ="'.$elig.'" AND Gait="'.$gait.'"';
    }elseif ($year != "" && $consno != "" && $gait != "") {
        $sql = 'SELECT * FROM cons_sales_elig WHERE Consno ="'.$consno.'" AND Year = '.$year.' AND Gait="'.$gait.'"';
    }elseif ($year != "" && $elig != "" && $gait != "") {
        $sql = 'SELECT * FROM cons_sales_elig WHERE Elig ="'.$elig.'" AND Year = '.$year.' AND Gait="'.$gait.'"';
    }elseif ($consno != "" && $elig != "" && $gait != "") {
        $sql = 'SELECT * FROM cons_sales_elig_allyear WHERE Consno ="'.$consno.'" AND Elig = "'.$elig.'" AND Gait="'.$gait.'"';
    }elseif ($year != "" && $consno != "" && $elig != "") {
        $sql = 'SELECT * FROM cons_sales_elig WHERE Consno ="'.$consno.'" AND Year = '.$year.' AND Elig ="'.$elig.'"';
    }elseif ($year != "" && $consno != "") {
        $sql = 'SELECT * FROM cons_sales WHERE Consno ="'.$consno.'" AND Year = '.$year;
    }elseif ($consno != "" && $elig != "") {
        $sql = 'SELECT * FROM cons_sales_elig_allyear WHERE Consno ="'.$consno.'" AND Elig ="'.$elig.'"';
    }elseif ($year != "" && $elig != "") {
        $sql = 'SELECT * FROM cons_sales_elig WHERE Elig ="'.$elig.'" AND Year = '.$year;
    }elseif ($year != "" && $gait != "") {
        $sql = 'SELECT * FROM cons_sales_elig WHERE Gait ="'.$gait.'" AND Year = '.$year;
    }elseif ($consno != "" && $gait != "") {
        $sql = 'SELECT * FROM cons_sales_elig_allyear WHERE Gait ="'.$gait.'" AND Consno = '.$consno;
    }elseif ($elig != "" && $gait != "") {
        $sql = 'SELECT * FROM cons_sales_elig_allyear WHERE Gait ="'.$gait.'" AND Elig = '.$elig;
    }elseif ($consno != "") {
        $sql = 'SELECT * FROM cons_sales_allyear WHERE Consno ="'.$consno.'"';
    }elseif ($year != "") {
        $sql = 'SELECT * FROM cons_sales WHERE Year = '.$year;
    }elseif ($elig != "") {
        $sql = 'SELECT * FROM cons_sales_elig_allyear WHERE Elig ="'.$elig.'"';
    }
    
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
        echo $sql;
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function fetchSireAnalysis($sire,$year,$elig,$gait)
{
    global $mysqli;
    $sql = 'SELECT * FROM sire_sales_allyear';
    
    if ($year != "" && $sire != "" && $elig != "" && $gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig WHERE Sire ="'.$sire.'" AND Year = '.$year.' AND
                Elig ="'.$elig.'" AND Gait="'.$gait.'"';
    }elseif ($year != "" && $sire != "" && $gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig WHERE Sire ="'.$sire.'" AND Year = '.$year.' AND Gait="'.$gait.'"';
    }elseif ($year != "" && $elig != "" && $gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig WHERE Elig ="'.$elig.'" AND Year = '.$year.' AND Gait="'.$gait.'"';
    }elseif ($sire != "" && $elig != "" && $gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear WHERE Sire ="'.$sire.'" AND Elig = "'.$elig.'" AND Gait="'.$gait.'"';
    }elseif ($year != "" && $sire != "" && $elig != "") {
        $sql = 'SELECT * FROM sire_sales_elig WHERE Sire ="'.$sire.'" AND Year = '.$year.' AND Elig ="'.$elig.'"';
    }elseif ($year != "" && $sire != "") {
        $sql = 'SELECT * FROM sire_sales WHERE Sire ="'.$sire.'" AND Year = '.$year;
    }elseif ($sire != "" && $elig != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear WHERE Sire ="'.$sire.'" AND Elig ="'.$elig.'"';
    }elseif ($year != "" && $elig != "") {
        $sql = 'SELECT * FROM sire_sales_elig WHERE Elig ="'.$elig.'" AND Year = '.$year;
    }elseif ($year != "" && $gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig WHERE Gait ="'.$gait.'" AND Year = '.$year;
    }elseif ($sire != "" && $gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear WHERE Gait ="'.$gait.'" AND Sire = '.$sire;
    }elseif ($elig != "" && $gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear WHERE Gait ="'.$gait.'" AND Elig = '.$elig;
    }elseif ($sire != "") {
        $sql = 'SELECT * FROM sire_sales_allyear WHERE Sire ="'.$sire.'"';
    }elseif ($year != "") {
        $sql = 'SELECT * FROM sire_sales WHERE `Year` = '.$year;
    }elseif ($elig != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear WHERE Elig ="'.$elig.'"';
    }
    
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
        echo $sql;
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

// function fetchSireAnalysis_tb($sire,$year,$elig,$gait, $sort1, $sort2, $sort3, $sort4, $sort5)
// {
//     global $mysqli;
//     $sql = 'SELECT * FROM sire_sales_allyear_tb';
    
//     if ($year != "" && $sire != "" && $elig != "" && $gait != "") {
//         $sql = 'SELECT * FROM sire_sales_elig_tb WHERE Sire ="'.$sire.'" AND Year = '.$year.' AND
//                 Elig ="'.$elig.'" AND Gait="'.$gait.'"';
//     }elseif ($year != "" && $sire != "" && $gait != "") {
//         $sql = 'SELECT * FROM sire_sales_elig_tb WHERE Sire ="'.$sire.'" AND Year = '.$year.' AND Gait="'.$gait.'"';
//     }elseif ($year != "" && $elig != "" && $gait != "") {
//         $sql = 'SELECT * FROM sire_sales_elig_tb WHERE Elig ="'.$elig.'" AND Year = '.$year.' AND Gait="'.$gait.'"';
//     }elseif ($sire != "" && $elig != "" && $gait != "") {
//         $sql = 'SELECT * FROM sire_sales_elig_allyear_tb WHERE Sire ="'.$sire.'" AND Elig = "'.$elig.'" AND Gait="'.$gait.'"';
//     }elseif ($year != "" && $sire != "" && $elig != "") {
//         $sql = 'SELECT * FROM sire_sales_elig_tb WHERE Sire ="'.$sire.'" AND Year = '.$year.' AND Elig ="'.$elig.'"';
//     }elseif ($year != "" && $sire != "") {
//         $sql = 'SELECT * FROM sire_sales_tb WHERE Sire ="'.$sire.'" AND Year = '.$year;
//     }elseif ($sire != "" && $elig != "") {
//         $sql = 'SELECT * FROM sire_sales_elig_allyear_tb WHERE Sire ="'.$sire.'" AND Elig ="'.$elig.'"';
//     }elseif ($year != "" && $elig != "") {
//         $sql = 'SELECT * FROM sire_sales_elig_tb WHERE Elig ="'.$elig.'" AND Year = '.$year;
//     }elseif ($year != "" && $gait != "") {
//         $sql = 'SELECT * FROM sire_sales_elig_tb WHERE Gait ="'.$gait.'" AND Year = '.$year;
//     }elseif ($sire != "" && $gait != "") {
//         $sql = 'SELECT * FROM sire_sales_elig_allyear_tb WHERE Gait ="'.$gait.'" AND Sire = '.$sire;
//     }elseif ($elig != "" && $gait != "") {
//         $sql = 'SELECT * FROM sire_sales_elig_allyear_tb WHERE Gait ="'.$gait.'" AND Elig = '.$elig;
//     }elseif ($sire != "") {
//         $sql = 'SELECT * FROM sire_sales_allyear_tb WHERE Sire ="'.$sire.'"';
//     }elseif ($year != "") {
//         $sql = 'SELECT * FROM sire_sales_tb WHERE `Year` = '.$year;
//     }elseif ($elig != "") {
//         $sql = 'SELECT * FROM sire_sales_elig_allyear_tb WHERE Elig ="'.$elig.'"';
//     }

//     $orderby1 = ' ORDER BY '.$sort1;
//     $orderby2 = ', '.$sort2;
//     $orderby3 = ', '.$sort3;
//     $orderby4 = ', '.$sort4;
//     $orderby5 = ', '.$sort5;
    
//     if ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !="" && $sort5 !="") {
//         $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4.$orderby5;
//     }elseif ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !=""){
//         $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4;
//     }elseif ($sort1 !="" && $sort2 !="" && $sort3 !=""){
//         $sql = $sql.$orderby1.$orderby2.$orderby3;
//     }elseif ($sort1 !="" && $sort2 !=""){
//         $sql = $sql.$orderby1.$orderby2;
//     }elseif ($sort1 !=""){
//         $sql = $sql.$orderby1;
//     }
    
//     $result = mysqli_query($mysqli, $sql);
//     if (!$result) {
//         printf("Errormessage: %s\n", $mysqli->error);
//         echo $sql;
//     }
//     $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
//     return $json;
// }

function fetchSireAnalysis_tb($sire,$year,$elig,$gait)
{
    global $mysqli;
    $sql = 'SELECT * FROM sire_sales_allyear_tb';
    
    if ($year != "" && $sire != "" && $elig != "" && $gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig_tb WHERE Sire ="'.$sire.'" AND Year = '.$year.' AND
                Elig ="'.$elig.'" AND Gait="'.$gait.'"';
    }elseif ($year != "" && $sire != "" && $gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig_tb WHERE Sire ="'.$sire.'" AND Year = '.$year.' AND Gait="'.$gait.'"';
    }elseif ($year != "" && $elig != "" && $gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig_tb WHERE Elig ="'.$elig.'" AND Year = '.$year.' AND Gait="'.$gait.'"';
    }elseif ($sire != "" && $elig != "" && $gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear_tb WHERE Sire ="'.$sire.'" AND Elig = "'.$elig.'" AND Gait="'.$gait.'"';
    }elseif ($year != "" && $sire != "" && $elig != "") {
        $sql = 'SELECT * FROM sire_sales_elig_tb WHERE Sire ="'.$sire.'" AND Year = '.$year.' AND Elig ="'.$elig.'"';
    }elseif ($year != "" && $sire != "") {
        $sql = 'SELECT * FROM sire_sales_tb WHERE Sire ="'.$sire.'" AND Year = '.$year;
    }elseif ($sire != "" && $elig != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear_tb WHERE Sire ="'.$sire.'" AND Elig ="'.$elig.'"';
    }elseif ($year != "" && $elig != "") {
        $sql = 'SELECT * FROM sire_sales_elig_tb WHERE Elig ="'.$elig.'" AND Year = '.$year;
    }elseif ($year != "" && $gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig_tb WHERE Gait ="'.$gait.'" AND Year = '.$year;
    }elseif ($sire != "" && $gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear_tb WHERE Gait ="'.$gait.'" AND Sire = '.$sire;
    }elseif ($elig != "" && $gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear_tb WHERE Gait ="'.$gait.'" AND Elig = '.$elig;
    }elseif ($sire != "") {
        $sql = 'SELECT * FROM sire_sales_allyear_tb WHERE Sire ="'.$sire.'"';
    }elseif ($year != "") {
        $sql = 'SELECT * FROM sire_sales_tb WHERE `Year` = '.$year;
    }elseif ($elig != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear_tb WHERE Elig ="'.$elig.'"';
    }

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
        echo $sql;
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}


function fetchSireAnalysisSummary($year,$elig,$gait,$sort1,$sort2,$sort3,$sort4,$sort5)
{
    global $mysqli;
    $select = 'SELECT
    Sire,
    Elig,
    Gait,
    Count,
    A.Total,
    A.Avg,
    Top,
    CCount,
    CTotal,
    CAvg,
    CTop,
    FCount,
    FTotal,
    FAvg,
    FTop,
    SireAvgRank,
    SireGrossRank,
    PacerAvgRank,
    PacerGrossRank,
    TrotterAvgRank,
    TrotterGrossRank FROM';
    
    $sql_elig= $select.' (
        (SELECT * FROM sire_sales_elig) A
        LEFT JOIN
        (SELECT Avg ,(@CurRank := @CurRank + 1) AS SireAvgRank From (SELECT Avg
            FROM sire_sales_elig WHERE Year='.$year.' GROUP BY Avg ORDER BY Avg DESC) as a,(SELECT @curRank := 0) r) B
            ON A.Avg=B.Avg
        LEFT JOIN
        (SELECT Total ,(@CurRank1 := @CurRank1 + 1) AS SireGrossRank From (SELECT Total
            FROM sire_sales_elig WHERE Year='.$year.' GROUP BY Total ORDER BY Total DESC) as a,(SELECT @curRank1 := 0) r) C
            ON A.Total=C.Total
        LEFT JOIN
        (SELECT Avg ,(@curRank2 := @curRank2 + 1) AS PacerAvgRank From (SELECT Avg
    		FROM sire_sales_elig WHERE Gait="P" AND Year='.$year.' GROUP BY Avg ORDER BY Avg DESC) as a,(SELECT @curRank2 := 0) r) D
            ON A.Avg=D.Avg and A.Gait="P"
        LEFT JOIN
        (SELECT Total ,(@curRank3 := @curRank3 + 1) AS PacerGrossRank From (SELECT Total
    		FROM sire_sales_elig WHERE Gait="P" AND Year='.$year.' GROUP BY Total ORDER BY Total DESC) as a,(SELECT @curRank3 := 0) r) E
            ON A.Total=E.Total and A.Gait="P"
        LEFT JOIN
        (SELECT Avg ,(@curRank4 := @curRank4 + 1) AS TrotterAvgRank From (SELECT Avg
    		FROM sire_sales_elig WHERE Gait="T" AND Year='.$year.' GROUP BY Avg ORDER BY Avg DESC) as a,(SELECT @curRank4 := 0) r) F
            ON A.Avg=F.Avg and A.Gait="T"
        LEFT JOIN
        (SELECT Total ,(@curRank5 := @curRank5 + 1) AS TrotterGrossRank From (SELECT Total
    		FROM sire_sales_elig WHERE Gait="T" AND Year='.$year.' GROUP BY Total ORDER BY Total DESC) as a,(SELECT @curRank5 := 0) r) G
            ON A.Total=G.Total and A.Gait="T")';
    
    
    $sql_elig_allyear= $select.' (
        (SELECT * FROM sire_sales_elig_allyear) A
        LEFT JOIN
        (SELECT Avg ,(@CurRank := @CurRank + 1) AS SireAvgRank From (SELECT Avg
            FROM sire_sales_elig_allyear GROUP BY Avg ORDER BY Avg DESC) as a,(SELECT @curRank := 0) r) B
            ON A.Avg=B.Avg
        LEFT JOIN
        (SELECT Total ,(@CurRank1 := @CurRank1 + 1) AS SireGrossRank From (SELECT Total
            FROM sire_sales_elig_allyear GROUP BY Total ORDER BY Total DESC) as a,(SELECT @curRank1 := 0) r) C
            ON A.Total=C.Total
        LEFT JOIN
        (SELECT Avg ,(@curRank2 := @curRank2 + 1) AS PacerAvgRank From (SELECT Avg
    		FROM sire_sales_elig_allyear WHERE Gait="P" GROUP BY Avg ORDER BY Avg DESC) as a,(SELECT @curRank2 := 0) r) D
            ON A.Avg=D.Avg and A.Gait="P"
        LEFT JOIN
        (SELECT Total ,(@curRank3 := @curRank3 + 1) AS PacerGrossRank From (SELECT Total
    		FROM sire_sales_elig_allyear WHERE Gait="P" GROUP BY Total ORDER BY Total DESC) as a,(SELECT @curRank3 := 0) r) E
            ON A.Total=E.Total and A.Gait="P"
        LEFT JOIN
        (SELECT Avg ,(@curRank4 := @curRank4 + 1) AS TrotterAvgRank From (SELECT Avg
    		FROM sire_sales_elig_allyear WHERE Gait="T" GROUP BY Avg ORDER BY Avg DESC) as a,(SELECT @curRank4 := 0) r) F
            ON A.Avg=F.Avg and A.Gait="T"
        LEFT JOIN
        (SELECT Total ,(@curRank5 := @curRank5 + 1) AS TrotterGrossRank From (SELECT Total
    		FROM sire_sales_elig_allyear WHERE Gait="T" GROUP BY Total ORDER BY Total DESC) as a,(SELECT @curRank5 := 0) r) G
            ON A.Total=G.Total and A.Gait="T")';
    $sql = $sql_elig_allyear;
    //     if ($year != "" && $elig != "") {
    //         $sql = $sql_elig.' WHERE Elig ="'.$elig.'" AND Year = '.$year;
    //     }elseif ($year != "") {
    //         $sql = $sql_elig.' WHERE Year = '.$year;
    //     }elseif ($elig != "") {
    //         $sql = $sql_elig_allyear.' WHERE Elig ="'.$elig.'"';
    //     }
    if ($year != "" && $elig != "" && $gait != "") {
        $sql = $sql_elig.' WHERE Elig ="'.$elig.'" AND Year = '.$year.' AND Gait = "'.$gait.'"';
    }elseif ($year != "" && $elig) {
        $sql = $sql_elig.' WHERE Elig ="'.$elig.'" AND Year = '.$year;
    }elseif ($elig != "" && $gait != "") {
        $sql = $sql_elig_allyear.' WHERE Elig ="'.$elig.'" AND Gait = "'.$gait.'"';
    }elseif ($year != "" && $gait != "") {
        $sql = $sql_elig.' WHERE Gait ="'.$gait.'" AND Year = '.$year;
    }elseif ($elig != "") {
        $sql = $sql_elig_allyear.' WHERE Elig ="'.$elig.'"';
    }elseif ($year != "") {
        $sql = $sql_elig.' WHERE Year = '.$year;
    }elseif ($gait != "") {
        $sql = $sql_elig_allyear.' WHERE Gait ="'.$gait.'"';
    }
    
    $orderby1 = ' ORDER BY '.$sort1;
    $orderby2 = ', '.$sort2;
    $orderby3 = ', '.$sort3;
    $orderby4 = ', '.$sort4;
    $orderby5 = ', '.$sort5;
    
    if ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !="" && $sort5 !="") {
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4.$orderby5;
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4;
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3;
    }elseif ($sort1 !="" && $sort2 !=""){
        $sql = $sql.$orderby1.$orderby2;
    }elseif ($sort1 !=""){
        $sql = $sql.$orderby1;
    }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}


function fetchSireAnalysisSummary_tb($year,$elig,$gait,$sort1,$sort2,$sort3,$sort4,$sort5)
{
    global $mysqli;
    $select = 'SELECT
    Sire,
    Elig,
    Count,
    A.Total,
    A.Avg,
    Top,
    CCount,
    CTotal,
    CAvg,
    CTop,
    FCount,
    FTotal,
    FAvg,
    FTop,
    SireAvgRank,
    SireGrossRank FROM';
    
    $sql_elig= $select.' (
        (SELECT * FROM sire_sales_elig_tb) A
        LEFT JOIN
        (SELECT Avg ,(@CurRank := @CurRank + 1) AS SireAvgRank From (SELECT Avg
            FROM sire_sales_elig_tb WHERE Year='.$year.' GROUP BY Avg ORDER BY Avg DESC) as a,(SELECT @curRank := 0) r) B
            ON A.Avg=B.Avg
        LEFT JOIN
        (SELECT Total ,(@CurRank1 := @CurRank1 + 1) AS SireGrossRank From (SELECT Total
            FROM sire_sales_elig_tb WHERE Year='.$year.' GROUP BY Total ORDER BY Total DESC) as a,(SELECT @curRank1 := 0) r) C
            ON A.Total=C.Total
        LEFT JOIN
        (SELECT Avg ,(@curRank2 := @curRank2 + 1) AS PacerAvgRank From (SELECT Avg
    		FROM sire_sales_elig_tb WHERE Gait="P" AND Year='.$year.' GROUP BY Avg ORDER BY Avg DESC) as a,(SELECT @curRank2 := 0) r) D
            ON A.Avg=D.Avg and A.Gait="P"
        LEFT JOIN
        (SELECT Total ,(@curRank3 := @curRank3 + 1) AS PacerGrossRank From (SELECT Total
    		FROM sire_sales_elig_tb WHERE Gait="P" AND Year='.$year.' GROUP BY Total ORDER BY Total DESC) as a,(SELECT @curRank3 := 0) r) E
            ON A.Total=E.Total and A.Gait="P"
        LEFT JOIN
        (SELECT Avg ,(@curRank4 := @curRank4 + 1) AS TrotterAvgRank From (SELECT Avg
    		FROM sire_sales_elig_tb WHERE Gait="T" AND Year='.$year.' GROUP BY Avg ORDER BY Avg DESC) as a,(SELECT @curRank4 := 0) r) F
            ON A.Avg=F.Avg and A.Gait="T"
        LEFT JOIN
        (SELECT Total ,(@curRank5 := @curRank5 + 1) AS TrotterGrossRank From (SELECT Total
    		FROM sire_sales_elig_tb WHERE Gait="T" AND Year='.$year.' GROUP BY Total ORDER BY Total DESC) as a,(SELECT @curRank5 := 0) r) G
            ON A.Total=G.Total and A.Gait="T")';
    
    
    $sql_elig_allyear= $select.' (
        (SELECT * FROM sire_sales_elig_allyear_tb) A
        LEFT JOIN
        (SELECT Avg ,(@CurRank := @CurRank + 1) AS SireAvgRank From (SELECT Avg
            FROM sire_sales_elig_allyear_tb GROUP BY Avg ORDER BY Avg DESC) as a,(SELECT @curRank := 0) r) B
            ON A.Avg=B.Avg
        LEFT JOIN
        (SELECT Total ,(@CurRank1 := @CurRank1 + 1) AS SireGrossRank From (SELECT Total
            FROM sire_sales_elig_allyear_tb GROUP BY Total ORDER BY Total DESC) as a,(SELECT @curRank1 := 0) r) C
            ON A.Total=C.Total
        LEFT JOIN
        (SELECT Avg ,(@curRank2 := @curRank2 + 1) AS PacerAvgRank From (SELECT Avg
    		FROM sire_sales_elig_allyear_tb WHERE Gait="P" GROUP BY Avg ORDER BY Avg DESC) as a,(SELECT @curRank2 := 0) r) D
            ON A.Avg=D.Avg and A.Gait="P"
        LEFT JOIN
        (SELECT Total ,(@curRank3 := @curRank3 + 1) AS PacerGrossRank From (SELECT Total
    		FROM sire_sales_elig_allyear_tb WHERE Gait="P" GROUP BY Total ORDER BY Total DESC) as a,(SELECT @curRank3 := 0) r) E
            ON A.Total=E.Total and A.Gait="P"
        LEFT JOIN
        (SELECT Avg ,(@curRank4 := @curRank4 + 1) AS TrotterAvgRank From (SELECT Avg
    		FROM sire_sales_elig_allyear_tb WHERE Gait="T" GROUP BY Avg ORDER BY Avg DESC) as a,(SELECT @curRank4 := 0) r) F
            ON A.Avg=F.Avg and A.Gait="T"
        LEFT JOIN
        (SELECT Total ,(@curRank5 := @curRank5 + 1) AS TrotterGrossRank From (SELECT Total
    		FROM sire_sales_elig_allyear_tb WHERE Gait="T" GROUP BY Total ORDER BY Total DESC) as a,(SELECT @curRank5 := 0) r) G
            ON A.Total=G.Total and A.Gait="T")';
    $sql = $sql_elig_allyear;
    //     if ($year != "" && $elig != "") {
    //         $sql = $sql_elig.' WHERE Elig ="'.$elig.'" AND Year = '.$year;
    //     }elseif ($year != "") {
    //         $sql = $sql_elig.' WHERE Year = '.$year;
    //     }elseif ($elig != "") {
    //         $sql = $sql_elig_allyear.' WHERE Elig ="'.$elig.'"';
    //     }
    if ($year != "" && $elig != "" && $gait != "") {
        $sql = $sql_elig.' WHERE Elig ="'.$elig.'" AND Year = '.$year.' AND Gait = "'.$gait.'"';
    }elseif ($year != "" && $elig) {
        $sql = $sql_elig.' WHERE Elig ="'.$elig.'" AND Year = '.$year;
    }elseif ($elig != "" && $gait != "") {
        $sql = $sql_elig_allyear.' WHERE Elig ="'.$elig.'" AND Gait = "'.$gait.'"';
    }elseif ($year != "" && $gait != "") {
        $sql = $sql_elig.' WHERE Gait ="'.$gait.'" AND Year = '.$year;
    }elseif ($elig != "") {
        $sql = $sql_elig_allyear.' WHERE Elig ="'.$elig.'"';
    }elseif ($year != "") {
        $sql = $sql_elig.' WHERE Year = '.$year;
    }elseif ($gait != "") {
        $sql = $sql_elig_allyear.' WHERE Gait ="'.$gait.'"';
    }
    
    $orderby1 = ' ORDER BY '.$sort1;
    $orderby2 = ', '.$sort2;
    $orderby3 = ', '.$sort3;
    $orderby4 = ', '.$sort4;
    $orderby5 = ', '.$sort5;
    
    if ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !="" && $sort5 !="") {
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4.$orderby5;
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4;
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3;
    }elseif ($sort1 !="" && $sort2 !=""){
        $sql = $sql.$orderby1.$orderby2;
    }elseif ($sort1 !=""){
        $sql = $sql.$orderby1;
    }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function fetchHorseList()
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Horse from sales
            UNION
            SELECT DISTINCT dam from damsire
            UNION
            SELECT DISTINCT damofdam from damsire';

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function fetchSireList($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Sire FROM sire_sales';
    if ($year != "") {
        $sql = 'SELECT DISTINCT Sire FROM sire_sales WHERE `Year` = "'.$year.'"';
    }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function fetchConsnoList($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Consno FROM cons_sales';
    if ($year != "") {
        $sql = 'SELECT DISTINCT Consno FROM cons_sales WHERE `Year` = "'.$year.'"';
    }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function fetchSireList_tb($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Sire FROM sire_sales_tb';
    if ($year != "") {
        $sql = 'SELECT DISTINCT Sire FROM sire_sales_tb WHERE `Year` = "'.$year.'"';
    }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function fetchSireListAll($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Sire FROM damsire';
//     if ($year != "") {
//         $sql = 'SELECT DISTINCT Sire FROM sales WHERE Year(saledate) = "'.$year.'"';
//     }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function fetchSireListAll_tb($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Sire FROM tdamsire';
//     if ($year != "") {
//         $sql = 'SELECT DISTINCT Sire FROM sales_tb WHERE Year(saledate) = "'.$year.'"';
//     }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function getSexList()
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Sex FROM sales';
 
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function getSexList_tb()
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Sex FROM tsales';

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function getBredtoList($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Bredto FROM sales order by Bredto';
    if ($year != "") {
        $sql = 'SELECT DISTINCT Bredto FROM sales WHERE Year(saledate) = "'.$year.'" order by Bredto';
    }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function getBredtoList_tb($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Bredto FROM tsales';
    if ($year != "") {
        $sql = 'SELECT DISTINCT Bredto FROM tsales WHERE Year(saledate) = "'.$year.'"';
    }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}


function getYearsList() {
    global $mysqli;
    $sql = 'SELECT DISTINCT Year(saledate) AS `Year` FROM sales ORDER BY Year(saledate) DESC;';
    
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}


function getYearsList_tb() {
    global $mysqli;
    $sql = 'SELECT DISTINCT Year(saledate) AS `Year` FROM tsales ORDER BY Year(saledate) DESC;';
    
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}
function getEligList() {
    global $mysqli;
    $sql = 'select distinct Elig FROM sales WHERE PRICE>0 ORDER BY Elig';
    
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function getEligList_tb() {
    global $mysqli;
    $sql = 'select distinct Elig FROM tsales WHERE PRICE>0 ORDER BY Elig';
    
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function getGaitList() {
    global $mysqli;
    $sql = 'select distinct Gait FROM sales WHERE PRICE>0 ORDER BY Gait';
    
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function getGaitList_tb() {
    global $mysqli;
    $sql = 'SELECT DISTINCT Gait FROM tsales WHERE PRICE>0 ORDER BY Gait';
    
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function fetchBuyersReport($salecode,$year,$type,$sort1,$sort2,$sort3,$sort4,$sort5)
{
    global $mysqli;
    $sql = 'SELECT 
    HIP,
    Horse,
    Type,
    Price,
    Currency,
    Salecode,
    Day,
    Purlname,
    Purfname,
    Sbcity,
    Sbstate,
    Sbcountry
    FROM sales WHERE Price>0 ';
    
    $searchSalecode = ' AND Salecode="'.$salecode.'"';
    $searchYear = ' AND YEAR(`SALEDATE`)='.$year;
    $searchType = ' AND Type="'.$type.'"';
    
    $orderby1 = ' ORDER BY '.$sort1;
    $orderby2 = ', '.$sort2;
    $orderby3 = ', '.$sort3;
    $orderby4 = ', '.$sort4;
    $orderby5 = ', '.$sort5;
    
    if ($year != "" && $salecode != "" && $type != "") {
        $sql = $sql.$searchSalecode.$searchType.$searchYear;
    }elseif ($year != "" && $salecode) {
        $sql = $sql.$searchSalecode.$searchYear;
    }elseif ($salecode != "" && $type != "") {
        $sql = $sql.$searchSalecode.$searchType;
    }elseif ($year != "" && $type != "") {
        $sql = $sql.$searchType.$searchYear;
    }elseif ($salecode != "") {
        $sql = $sql.$searchSalecode;
    }elseif ($year != "") {
        $sql = $sql.$searchYear;
    }elseif ($type != "") {
        $sql = $sql.$searchType;
    }else
        return "";
    
    if ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !="" && $sort5 !="") {
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4.$orderby5;
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4;
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3;
    }elseif ($sort1 !="" && $sort2 !=""){
        $sql = $sql.$orderby1.$orderby2;
    }elseif ($sort1 !=""){
        $sql = $sql.$orderby1;
    }
    $result = mysqli_query($mysqli, $sql);
    //echo $sql;
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error.'--SQL--'.$sql);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function fetchBuyersReport_tb($salecode,$year,$type,$sort1,$sort2,$sort3,$sort4,$sort5)
{
    global $mysqli;
    $sql = 'SELECT
    HIP,
    Horse,
    Type,
    Price,
    Currency,
    Salecode,
    Day,
    Purlname,
    Purfname,
    Sbcity,
    Sbstate,
    Sbcountry
    FROM tsales WHERE Price>0 ';
    
    $searchSalecode = ' AND Salecode="'.$salecode.'"';
    $searchYear = ' AND YEAR(`SALEDATE`)='.$year;
    $searchType = ' AND Type="'.$type.'"';
    
    $orderby1 = ' ORDER BY '.$sort1;
    $orderby2 = ', '.$sort2;
    $orderby3 = ', '.$sort3;
    $orderby4 = ', '.$sort4;
    $orderby5 = ', '.$sort5;
    
    if ($year != "" && $salecode != "" && $type != "") {
        $sql = $sql.$searchSalecode.$searchType.$searchYear;
    }elseif ($year != "" && $salecode) {
        $sql = $sql.$searchSalecode.$searchYear;
    }elseif ($salecode != "" && $type != "") {
        $sql = $sql.$searchSalecode.$searchType;
    }elseif ($year != "" && $type != "") {
        $sql = $sql.$searchType.$searchYear;
    }elseif ($salecode != "") {
        $sql = $sql.$searchSalecode;
    }elseif ($year != "") {
        $sql = $sql.$searchYear;
    }elseif ($type != "") {
        $sql = $sql.$searchType;
    }
        
    if ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !="" && $sort5 !="") {
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4.$orderby5;
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4;
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3;
    }elseif ($sort1 !="" && $sort2 !=""){
        $sql = $sql.$orderby1.$orderby2;
    }elseif ($sort1 !=""){
        $sql = $sql.$orderby1;
    }
    $result = mysqli_query($mysqli, $sql);
    //echo $sql;
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error.'--SQL--'.$sql);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function fetchSalecodeList($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Salecode FROM sales 
            WHERE Salecode<> "" 
            ORDER BY Salecode';
    if ($year != "") {
        $sql = 'SELECT DISTINCT Salecode FROM sales WHERE YEAR(`SALEDATE`) = "'.$year.'"
                AND Salecode<> "" 
                ORDER BY Salecode';
    }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function fetchSalecodeList_tb($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Salecode FROM tsales
            WHERE Salecode<> ""
            ORDER BY Salecode';
    if ($year != "") {
        $sql = 'SELECT DISTINCT Salecode FROM tsales WHERE YEAR(`SALEDATE`) = "'.$year.'"
                AND Salecode<> ""
                ORDER BY Salecode';
    }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function fetchSalecodeWithoutYear($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT left(Salecode,4) as Salecode FROM sales
            WHERE Salecode<> ""
            Group By left(salecode,4)
            ORDER BY Salecode ;';
    if ($year != "") {
        $sql = 'SELECT DISTINCT left(Salecode,4) as Salecode FROM sales WHERE YEAR(`SALEDATE`) = "'.$year.'"
                AND Salecode<> ""
                Group by left(Salecode,4)
                ORDER BY Salecode ';
    }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}


function fetchSalecodeWithoutYear_tb($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT left(Salecode,4) as Salecode FROM tsales
            WHERE Salecode<> ""
            Group By left(salecode,4)
            ORDER BY Salecode ;';
    if ($year != "") {
        $sql = 'SELECT DISTINCT left(Salecode,4) as Salecode FROM tsales WHERE YEAR(`SALEDATE`) = "'.$year.'"
                AND Salecode<> ""
                Group by left(Salecode,4)
                ORDER BY Salecode ';
    }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function fetchTypeList()
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Type FROM sales
            WHERE Type<> ""
            ORDER BY Type';
    
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function fetchTypeList_tb()
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Type FROM tsales
            WHERE Type<> ""
            ORDER BY Type';
    
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function fetchSalesReport($salecode,$year,$type,$gait,$sex,$sire,$bredto,$sort1,$sort2,$sort3,$sort4,$sort5)
{
    global $mysqli;
    
    if ($year == "" && $salecode == "" && $type == "" && $gait == "" && $sex == "" && $sire == "" && $bredto == "") {
        return "";
    }
    $searchParam = ' AND YEAR(Saledate)= IF("'.$year.'" = "", YEAR(Saledate), "'.$year.'")
                     AND Salecode= IF("'.$salecode.'"  = "", Salecode, "'.$salecode.'")
                     AND Type= IF("'.$type.'"  = "", Type, "'.$type.'")
                     AND Gait= IF("'.$gait.'"  = "", Gait, "'.$gait.'")
                     AND Sex= IF("'.$sex.'"  = "", Sex, "'.$sex.'")
                     AND b.Sire= IF("'.$sire.'"  = "", b.Sire, "'.$sire.'")
                     AND Bredto= IF("'.$bredto.'"  = "", Bredto, "'.$bredto.'") ';
    
    $sql = 'SELECT
    HIP,
    Horse,
    Sex,
    Type,
    Gait,
    Price,
    Currency,
    Salecode,
    Day,
    Consno,
    b.Sire,
    b.Dam,
    Bredto,
    LastBred,
    Age,
    Rating
    FROM sales a
    LEFT JOIN damsire b
    ON a.damsire_Id=b.damsire_ID WHERE Price>0 '.$searchParam;
    
    
//     $searchSalecode = ' AND Salecode="'.$salecode.'"';
//     $searchYear = ' AND YEAR(`SALEDATE`)='.$year;
//     $searchType = ' AND Type="'.$type.'"';
//     $searchGait = ' AND Gait="'.$Gait.'"';
    
    $orderby1 = ' ORDER BY '.$sort1;
    $orderby2 = ', '.$sort2;
    $orderby3 = ', '.$sort3;
    $orderby4 = ', '.$sort4;
    $orderby5 = ', '.$sort5;
    
//     if ($year != "" && $salecode != "" && $type != "") {
//         $sql = $sql.$searchSalecode.$searchType.$searchYear;
//     }elseif ($year != "" && $salecode) {
//         $sql = $sql.$searchSalecode.$searchYear;
//     }elseif ($salecode != "" && $type != "") {
//         $sql = $sql.$searchSalecode.$searchType;
//     }elseif ($year != "" && $type != "") {
//         $sql = $sql.$searchType.$searchYear;
//     }elseif ($salecode != "") {
//         $sql = $sql.$searchSalecode;
//     }elseif ($year != "") {
//         $sql = $sql.$searchYear;
//     }elseif ($type != "") {
//         $sql = $sql.$searchType;
//     }else
//         return "";
        
    if ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !="" && $sort5 !="") {
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4.$orderby5;
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4;
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3;
    }elseif ($sort1 !="" && $sort2 !=""){
        $sql = $sql.$orderby1.$orderby2;
    }elseif ($sort1 !=""){
        $sql = $sql.$orderby1;
    }
    $result = mysqli_query($mysqli, $sql);
    //echo $sql;
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error.'--SQL--'.$sql);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function fetchBroodmaresReport($salecode,$year,$type,$gait,$sex,$sire,$bredto,$sort1,$sort2,$sort3,$sort4,$sort5)
{
    global $mysqli;
    
    if ($year == "" && $salecode == "" && $type == "" && $gait == "" && $sex == "" && $sire == "" && $bredto == "") {
        return "";
    }
    $searchParam = ' AND YEAR(Saledate)= IF("'.$year.'" = "", YEAR(Saledate), "'.$year.'")
                     AND Salecode= IF("'.$salecode.'"  = "", Salecode, "'.$salecode.'")
                     AND Type= IF("'.$type.'"  = "", Type, "'.$type.'")
                     AND Gait= IF("'.$gait.'"  = "", Gait, "'.$gait.'")
                     AND Sex= IF("'.$sex.'"  = "", Sex, "'.$sex.'")
                     AND b.Sire= IF("'.$sire.'"  = "", b.Sire, "'.$sire.'")
                     AND Bredto= IF("'.$bredto.'"  = "", Bredto, "'.$bredto.'") ';
    
    $sql = 'SELECT
    HIP,
    Horse,
    Sex,
    Type,
    Gait,
    Price,
    Currency,
    Salecode,
    Day,
    Consno,


    Bredto,
    LastBred,
    Age,
    Rating
    FROM sales a
    LEFT JOIN damsire b
    ON a.damsire_Id=b.damsire_ID WHERE Price>0 '.$searchParam;
    
    
    $orderby1 = ' ORDER BY '.$sort1.' ASC';
    $orderby2 = ', '.$sort2.' ASC';
    $orderby3 = ', '.$sort3.' ASC';
    $orderby4 = ', '.$sort4.' ASC';
    $orderby5 = ', '.$sort5.' ASC';
    
    
    if ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !="" && $sort5 !="") {
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4.$orderby5;
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4;
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3;
    }elseif ($sort1 !="" && $sort2 !=""){
        $sql = $sql.$orderby1.$orderby2;
    }elseif ($sort1 !=""){
        $sql = $sql.$orderby1;
    }
    $result = mysqli_query($mysqli, $sql);
    //echo $sql;
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error.'--SQL--'.$sql);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function fetchWeanlingReport($salecode,$year,$type,$gait,$sex,$sire,$bredto,$sort1,$sort2,$sort3,$sort4,$sort5)
{
    global $mysqli;

    if ($year == "" && $salecode == "" && $type == "" && $gait == "" && $sex == "" && $sire == "" && $bredto == "") {
        return "";
    }
    
    $searchParam = ' AND YEAR(Saledate)= IF("'.$year.'" = "", YEAR(Saledate), "'.$year.'")
                     AND Salecode= IF("'.$salecode.'"  = "", Salecode, "'.$salecode.'")
                     AND Type= IF("'.$type.'"  = "", Type, "'.$type.'")
                     AND Gait= IF("'.$gait.'"  = "", Gait, "'.$gait.'")
                     AND Sex= IF("'.$sex.'"  = "", Sex, "'.$sex.'")
                     AND b.Sire= IF("'.$sire.'"  = "", b.Sire, "'.$sire.'")
                     AND Bredto= IF("'.$bredto.'"  = "", Bredto, "'.$bredto.'") ';
    
    $sql = 'SELECT
    HIP,
    Horse,
    b.Dam,
    Sex,
    Type,
    Price,
    Currency,
    Salecode,
    Day,
    Consno,
    saletype,
    Age,
    Rating
    FROM tsales a
    LEFT JOIN tdamsire b
    ON a.damsire_Id=b.damsire_ID WHERE Price>0'.$searchParam;
    
    
    $orderby1 = ' ORDER BY '.$sort1.' ASC';
    $orderby2 = ', '.$sort2.' ASC';
    $orderby3 = ', '.$sort3.' ASC';
    $orderby4 = ', '.$sort4.' ASC';
    $orderby5 = ', '.$sort5.' ASC';
    
    
    if ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !="" && $sort5 !="") {
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4.$orderby5;
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4;
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3;
    }elseif ($sort1 !="" && $sort2 !=""){
        $sql = $sql.$orderby1.$orderby2;
    }elseif ($sort1 !=""){
        $sql = $sql.$orderby1;
    }
    $result = mysqli_query($mysqli, $sql);
    //echo $sql;
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error.'--SQL--'.$sql);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function fetchBroodmaresReport_tb($salecode,$year,$type,$gait,$sex,$sire,$bredto,$sort1,$sort2,$sort3,$sort4,$sort5)
{
    global $mysqli;
    
    if ($year == "" && $salecode == "" && $type == "" && $gait == "" && $sex == "" && $sire == "" && $bredto == "") {
        return "";
    }
    $searchParam = ' AND YEAR(Saledate)= IF("'.$year.'" = "", YEAR(Saledate), "'.$year.'")
                     AND Salecode= IF("'.$salecode.'"  = "", Salecode, "'.$salecode.'")
                     AND Type= IF("'.$type.'"  = "", Type, "'.$type.'")
                     AND Gait= IF("'.$gait.'"  = "", Gait, "'.$gait.'")
                     AND Sex= IF("'.$sex.'"  = "", Sex, "'.$sex.'")
                     AND b.Sire= IF("'.$sire.'"  = "", b.Sire, "'.$sire.'")
                     AND Bredto= IF("'.$bredto.'"  = "", Bredto, "'.$bredto.'") ';
    
    $sql = 'SELECT
    HIP,
    Horse,
    Sex,
    Type,
    Gait,
    Price,
    Currency,
    Salecode,
    Day,
    Consno,


    Bredto,
    LastBred,
    Age,
    Rating
    FROM tsales a
    LEFT JOIN tdamsire b
    ON a.damsire_Id=b.damsire_ID WHERE Price>0 '.$searchParam;
    
    
    $orderby1 = ' ORDER BY '.$sort1.' ASC';
    $orderby2 = ', '.$sort2.' ASC';
    $orderby3 = ', '.$sort3.' ASC';
    $orderby4 = ', '.$sort4.' ASC';
    $orderby5 = ', '.$sort5.' ASC';
    
    
    if ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !="" && $sort5 !="") {
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4.$orderby5;
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4;
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3;
    }elseif ($sort1 !="" && $sort2 !=""){
        $sql = $sql.$orderby1.$orderby2;
    }elseif ($sort1 !=""){
        $sql = $sql.$orderby1;
    }
    $result = mysqli_query($mysqli, $sql);
    //echo $sql;
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error.'--SQL--'.$sql);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function fetchSalesReport_tb($salecode,$year,$type,$sort1,$sort2,$sort3,$sort4,$sort5)
{
    global $mysqli;
    
    $searchParam = ' AND YEAR(Saledate)= IF("'.$year.'" = "", YEAR(Saledate), "'.$year.'")
                     AND Salecode= IF("'.$salecode.'"  = "", Salecode, "'.$salecode.'")
                     AND Type= IF("'.$type.'"  = "", Type, "'.$type.'") ';
    
    $sql = 'SELECT
    HIP,
    Horse,
    `Type`,
    Price,
    Currency,
    Salecode,
    `Day`,
    Consno,
    b.Sire,
    b.Dam,
    Bredto,
    LastBred,
    Age,
    Rating
    FROM tsales a
    LEFT JOIN tdamsire b
    ON a.damsire_Id=b.damsire_ID WHERE Price>0 '.$searchParam;
    
    
    //     $searchSalecode = ' AND Salecode="'.$salecode.'"';
    //     $searchYear = ' AND YEAR(`SALEDATE`)='.$year;
    //     $searchType = ' AND Type="'.$type.'"';
    //     $searchGait = ' AND Gait="'.$Gait.'"';
    
    $orderby1 = ' ORDER BY '.$sort1;
    $orderby2 = ', '.$sort2;
    $orderby3 = ', '.$sort3;
    $orderby4 = ', '.$sort4;
    $orderby5 = ', '.$sort5;
    
    //     if ($year != "" && $salecode != "" && $type != "") {
    //         $sql = $sql.$searchSalecode.$searchType.$searchYear;
    //     }elseif ($year != "" && $salecode) {
    //         $sql = $sql.$searchSalecode.$searchYear;
    //     }elseif ($salecode != "" && $type != "") {
    //         $sql = $sql.$searchSalecode.$searchType;
    //     }elseif ($year != "" && $type != "") {
    //         $sql = $sql.$searchType.$searchYear;
    //     }elseif ($salecode != "") {
    //         $sql = $sql.$searchSalecode;
    //     }elseif ($year != "") {
    //         $sql = $sql.$searchYear;
    //     }elseif ($type != "") {
    //         $sql = $sql.$searchType;
    //     }else
        //         return "";
    
        if ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !="" && $sort5 !="") {
            $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4.$orderby5;
        }elseif ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !=""){
            $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4;
        }elseif ($sort1 !="" && $sort2 !="" && $sort3 !=""){
            $sql = $sql.$orderby1.$orderby2.$orderby3;
        }elseif ($sort1 !="" && $sort2 !=""){
            $sql = $sql.$orderby1.$orderby2;
        }elseif ($sort1 !=""){
            $sql = $sql.$orderby1;
        }
        $result = mysqli_query($mysqli, $sql);
        //echo $sql;
        if (!$result) {
            printf("Errormessage: %s\n", $mysqli->error.'--SQL--'.$sql);
        }
        $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
        return $json;
}

function fetchSalesAuctionReport($year,$type,$salecode)
{
    global $mysqli;
    
    $searchParam = ' AND YEAR(Saledate)= IF("'.$year.'" = "", YEAR(Saledate), "'.$year.'")
                     AND Type= IF("'.$type.'"  = "", Type, "'.$type.'")
                     AND left(Salecode,4)= IF("'.$salecode.'"  = "", left(Salecode,4), "'.$salecode.'") ';
    
    $sql = 'SELECT Salecode,Total, Gross, Avg, CONCAT(Total1, " - ",ROUND(Total1/Total*100,1)) AS V1,
            CONCAT(Total2, " - ",ROUND(Total2/Total*100,1)) AS V2,
            CONCAT(Total3, " - ",ROUND(Total3/Total*100,1)) AS V3, CONCAT(Total4, " - ",ROUND(Total4/Total*100,1)) AS V4, 
            CONCAT(Total5, " - ",ROUND(Total5/Total*100,1)) AS V5, CONCAT(Total6, " - ",ROUND(Total6/Total*100,1)) AS V6  FROM
            (SELECT Salecode,count(*) AS Total,sum(price) as Gross, avg(price) AS Avg FROM sales WHERE
            price>0 '.$searchParam.' group by salecode) A
            LEFT JOIN 
            (SELECT Salecode AS SC1,count(*) AS Total1 FROM sales WHERE 
            price>=100000 '.$searchParam.' group by salecode) B
            ON A.Salecode=B.SC1
            LEFT JOIN
            (SELECT Salecode AS SC2,count(*) AS Total2 FROM sales WHERE 
            price>=50000 and price<=99999 '.$searchParam.' group by salecode) C
            ON A.Salecode=C.SC2
            LEFT JOIN
            (SELECT Salecode AS SC3,count(*) AS Total3 FROM sales WHERE 
            price>=25000 and price<=49999 '.$searchParam.' group by salecode) D
            ON A.Salecode=D.SC3
            LEFT JOIN 
            (SELECT Salecode AS SC4,count(*) AS Total4 FROM sales WHERE 
            price>=10001 and price<=24999 '.$searchParam.' group by salecode) E
            ON A.Salecode=E.SC4
            LEFT JOIN 
            (SELECT Salecode AS SC5,count(*) AS Total5 FROM sales WHERE 
            price>=5000 and price<=10000 '.$searchParam.' group by salecode) F
            ON A.Salecode=F.SC5
            LEFT JOIN 
            (SELECT Salecode AS SC6,count(*) AS Total6 FROM sales WHERE 
            price<=4999 and price>0 '.$searchParam.' group by salecode) G
            ON A.Salecode=G.SC6';
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}


function fetchSalesAuctionReport_tb($year,$type,$salecode)
{
    global $mysqli;
    
    $searchParam = ' AND YEAR(Saledate)= IF("'.$year.'" = "", YEAR(Saledate), "'.$year.'")
                     AND Type= IF("'.$type.'"  = "", Type, "'.$type.'")
                     AND Salecode= IF("'.$salecode.'"  = "", Salecode, "'.$salecode.'") ';
    
    $sql = 'SELECT Salecode,Total, Gross, Avg, CONCAT(Total1, " - ",ROUND(Total1/Total*100,1)) AS V1,
            CONCAT(Total2, " - ",ROUND(Total2/Total*100,1)) AS V2,
            CONCAT(Total3, " - ",ROUND(Total3/Total*100,1)) AS V3, CONCAT(Total4, " - ",ROUND(Total4/Total*100,1)) AS V4,
            CONCAT(Total5, " - ",ROUND(Total5/Total*100,1)) AS V5, CONCAT(Total6, " - ",ROUND(Total6/Total*100,1)) AS V6  FROM
            (SELECT Salecode,count(*) AS Total,sum(price) as Gross, avg(price) AS Avg FROM tsales WHERE
            price>0 '.$searchParam.' group by salecode) A
            LEFT JOIN
            (SELECT Salecode AS SC1,count(*) AS Total1 FROM tsales WHERE
            price>=100000 '.$searchParam.' group by salecode) B
            ON A.Salecode=B.SC1
            LEFT JOIN
            (SELECT Salecode AS SC2,count(*) AS Total2 FROM tsales WHERE
            price>=50000 and price<=99999 '.$searchParam.' group by salecode) C
            ON A.Salecode=C.SC2
            LEFT JOIN
            (SELECT Salecode AS SC3,count(*) AS Total3 FROM tsales WHERE
            price>=25000 and price<=49999 '.$searchParam.' group by salecode) D
            ON A.Salecode=D.SC3
            LEFT JOIN
            (SELECT Salecode AS SC4,count(*) AS Total4 FROM tsales WHERE
            price>=10001 and price<=24999 '.$searchParam.' group by salecode) E
            ON A.Salecode=E.SC4
            LEFT JOIN
            (SELECT Salecode AS SC5,count(*) AS Total5 FROM tsales WHERE
            price>=5000 and price<=10000 '.$searchParam.' group by salecode) F
            ON A.Salecode=F.SC5
            LEFT JOIN
            (SELECT Salecode AS SC6,count(*) AS Total6 FROM tsales WHERE
            price<=4999 and price>0 '.$searchParam.' group by salecode) G
            ON A.Salecode=G.SC6';
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function fetchSalesSummary($year,$type,$salecode)
{
    global $mysqli;
    
    $searchParam = ' AND YEAR(Saledate)= IF("'.$year.'" = "", YEAR(Saledate), "'.$year.'")
                     AND Type= IF("'.$type.'"  = "", Type, "'.$type.'") 
                     AND left(Salecode,4)= IF("'.$salecode.'"  = "", left(Salecode,4), "'.$salecode.'") ';
    
    $sql = 'SELECT 
    a.Salecode,
    MAX(a.Horse) AS PACER,
    a.PMax,
    MAX(b.Horse) AS Trotter,
    b.TMax
FROM (
    SELECT 
        Salecode,
        Horse,
        MAX(Price) AS PMax
    FROM 
        sales 
    WHERE 
        GAIT = "P" '.$searchParam.'
    GROUP BY 
        Salecode, Horse
) AS a
LEFT JOIN (
    SELECT 
        Salecode,
        Horse,
        MAX(Price) AS TMax
    FROM 
        sales 
    WHERE 
        GAIT = "T" '.$searchParam.'
    GROUP BY 
        Salecode, Horse
) AS b ON a.Salecode = b.Salecode 
GROUP BY 
    a.Salecode, a.PMax, b.TMax;';

//     if ($year != "") {
//         $sql = "SELECT a.Salecode,a.Horse As PACER, a.Max AS PMax,b.Horse As Trotter,b.Max As TMax FROM
//     (SELECT Salecode, Horse, MAX(Price) AS Max FROM sales WHERE GAIT ='P' AND Type='Y' AND year(Saledate)=".$year."
//         GROUP BY Salecode ORDER BY Salecode) a
//     LEFT JOIN
//     (SELECT Salecode, Horse, MAX(Price) AS Max FROM sales WHERE GAIT ='T' AND Type='Y' AND year(Saledate)=".$year."
//         GROUP BY Salecode ORDER BY Salecode) b on a.Salecode=b.Salecode";
//     }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}


function fetchSalesSummary_tb($year,$type,$salecode)
{
    global $mysqli;
    
    $searchParam = ' AND YEAR(Saledate)= IF("'.$year.'" = "", YEAR(Saledate), "'.$year.'")
                     AND Type= IF("'.$type.'"  = "", Type, "'.$type.'")
                     AND left(Salecode,4)= IF("'.$salecode.'"  = "", left(Salecode,4), "'.$salecode.'") ';
    
    $sql = 'SELECT 
    a.Salecode,
    MAX(a.Horse) AS PACER,
    a.PMax,
    MAX(b.Horse) AS Trotter,
    b.TMax
FROM (
    SELECT 
        Salecode,
        Horse,
        MAX(Price) AS PMax
    FROM 
        tsales 
    WHERE 
        GAIT = "P" '.$searchParam.'
    GROUP BY 
        Salecode, Horse
) AS a
LEFT JOIN (
    SELECT 
        Salecode,
        Horse,
        MAX(Price) AS TMax
    FROM 
        tsales 
    WHERE 
        GAIT = "T" '.$searchParam.'
    GROUP BY 
        Salecode, Horse
) AS b ON a.Salecode = b.Salecode 
GROUP BY 
    a.Salecode, a.PMax, b.TMax;';
    
    //     if ($year != "") {
    //         $sql = "SELECT a.Salecode,a.Horse As PACER, a.Max AS PMax,b.Horse As Trotter,b.Max As TMax FROM
    //     (SELECT Salecode, Horse, MAX(Price) AS Max FROM sales WHERE GAIT ='P' AND Type='Y' AND year(Saledate)=".$year."
    //         GROUP BY Salecode ORDER BY Salecode) a
    //     LEFT JOIN
    //     (SELECT Salecode, Horse, MAX(Price) AS Max FROM sales WHERE GAIT ='T' AND Type='Y' AND year(Saledate)=".$year."
        //         GROUP BY Salecode ORDER BY Salecode) b on a.Salecode=b.Salecode";
    //     }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function fetchTopBuyers($year,$sort1,$sort2,$sort3,$sort4,$sort5)
{
    global $mysqli;
//     if ($year=="") {
//         $year=null;
//     } 
$sql = 'SELECT CONCAT(Purlname," ", Purfname) AS BuyerFullName, count(*) AS Total,SUM(Price) AS Gross,ROUND(Avg(Price),0) AS Avg
FROM sales WHERE Type="Y" AND Price>0 AND YEAR(Saledate)= IF("'.$year.'" = "", YEAR(Saledate), "'.$year.'") GROUP BY CONCAT(Purlname," ",Purfname)';

    
//     if ($year != "") {
//         $sql = 'SELECT Purlname AS BuyerLastName,Purfname AS BuyerFirstName,count(*) AS Total,SUM(Price) AS Gross,ROUND(Avg(Price),0) AS Avg
//         FROM sales WHERE Type="Y" AND Price>0 AND YEAR(Saledate)='.$year.'
//         GROUP BY CONCAT(Purlname," ",Purfname)';
//     }
    $orderby1 = ' ORDER BY '.$sort1;
    $orderby2 = ', '.$sort2;
    $orderby3 = ', '.$sort3;
    $orderby4 = ', '.$sort4;
    $orderby5 = ', '.$sort5;
    if ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !="" && $sort5 !="") {
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4.$orderby5;
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4;
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3;
    }elseif ($sort1 !="" && $sort2 !=""){
        $sql = $sql.$orderby1.$orderby2;
    }elseif ($sort1 !=""){
        $sql = $sql.$orderby1;
    }
    
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}


function fetchTopBuyers_tb($year,$sort1,$sort2,$sort3,$sort4,$sort5)
{
    global $mysqli;
    //     if ($year=="") {
    //         $year=null;
    //     }
    $sql = 'SELECT CONCAT(Purlname," ", Purfname) AS BuyerFullName, count(*) AS Total,SUM(Price) AS Gross,ROUND(Avg(Price),0) AS Avg
    FROM tsales WHERE Type="Y" AND Price>0 AND YEAR(Saledate)= IF("'.$year.'" = "", YEAR(Saledate), "'.$year.'") GROUP BY CONCAT(Purlname," ",Purfname)';
    
    //     if ($year != "") {
    //         $sql = 'SELECT Purlname AS BuyerLastName,Purfname AS BuyerFirstName,count(*) AS Total,SUM(Price) AS Gross,ROUND(Avg(Price),0) AS Avg
    //         FROM sales WHERE Type="Y" AND Price>0 AND YEAR(Saledate)='.$year.'
    //         GROUP BY CONCAT(Purlname," ",Purfname)';
    //     }
    $orderby1 = ' ORDER BY '.$sort1;
    $orderby2 = ', '.$sort2;
    $orderby3 = ', '.$sort3;
    $orderby4 = ', '.$sort4;
    $orderby5 = ', '.$sort5;
    if ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !="" && $sort5 !="") {
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4.$orderby5;
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4;
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3;
    }elseif ($sort1 !="" && $sort2 !=""){
        $sql = $sql.$orderby1.$orderby2;
    }elseif ($sort1 !=""){
        $sql = $sql.$orderby1;
    }
    
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function fetchIndividualSaleData($year,$salecode,$type,$elig,$gait,$sort1,$sort2,$sort3,$sort4,$sort5)
{
    global $mysqli;
    if ($year == "" && $salecode == "" && $type == "" && $elig == "" && $gait == "") {
        return "";
    }
    
    $searchParam = ' AND YEAR(Saledate)= IF("'.$year.'" = "", YEAR(Saledate), "'.$year.'")
                     AND Salecode= IF("'.$salecode.'"  = "", Salecode, "'.$salecode.'")
                     AND Type= IF("'.$type.'"  = "", Type, "'.$type.'")
                     AND Elig= IF("'.$elig.'"  = "", Elig, "'.$elig.'")
                     AND Gait= IF("'.$gait.'"  = "", Gait, "'.$gait.'") ';
    
    $sql =
    'SELECT ORank,Frank,CRank, HIP, Horse, Sex, Color, Gait, Type, ET,Datefoal, Elig, Sire, Dam, Salecode, Consno, Saledate, Day,
        a.Price, Currency, Purlname, Purfname, Rating FROM (
        SELECT
        HIP,
        Horse,
        Sex,
        Color,
        Gait,
        a.Type,
        ET,
        Datefoal,
        Elig,
        b.Sire,
        b.Dam,
        Salecode,
        Consno,
        Saledate,
        a.Day,
        Price,
        Currency,
        Purlname,
        Purfname,
        Rating
        FROM sales a
        JOIN damsire b ON a.damsire_Id=b.damsire_ID
        WHERE PRICE>0 '.$searchParam.') a 
    LEFT JOIN
    (SELECT Price AS Rankprice ,(@curRank := @curRank + 1) AS ORank from (
        SELECT Price FROM sales a
        JOIN damsire b ON a.damsire_Id=b.damsire_ID WHERE PRICE>0 '.$searchParam.'
        group by Price ORDER BY Price desc) as a,(SELECT @curRank := 0) r) b
        on a.price=b.Rankprice
    LEFT JOIN
    (select price  AS P1,sex AS S1,(@curRank1 := @curRank1 + 1) AS FRank from (
            SELECT price, sex FROM sales a
            JOIN damsire b ON a.damsire_Id=b.damsire_ID WHERE Sex IN ("F","M") AND PRICE>0 '.$searchParam.'
            group by price,sex ORDER BY price desc) as a,(SELECT @curRank1 := 0) r) c
            on a.price=c.P1 and a.Sex=c.S1
    LEFT JOIN
    (select price  AS P2,sex AS S2,(@curRank2 := @curRank2 + 1) AS CRank from (
            SELECT price, sex FROM sales a
            JOIN damsire b ON a.damsire_Id=b.damsire_ID WHERE Sex IN ("C","H","G") AND PRICE>0 '.$searchParam.'
            group by price,sex ORDER BY price desc) as a,(SELECT @curRank2 := 0) r) d
            on a.price=d.P2 and a.Sex=d.S2';
                     
    $orderby1 = ' ORDER BY '.$sort1;
    $orderby2 = ', '.$sort2;
    $orderby3 = ', '.$sort3;
    $orderby4 = ', '.$sort4;
    $orderby5 = ', '.$sort5;
    
    if ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !="" && $sort5 !="") {
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4.$orderby5;
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4;
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3;
    }elseif ($sort1 !="" && $sort2 !=""){
        $sql = $sql.$orderby1.$orderby2;
    }elseif ($sort1 !=""){
        $sql = $sql.$orderby1;
    }
    
    //echo $sql;
    $result = mysqli_query($mysqli, $sql);
    
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
        echo $sql;
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
    
}

function fetchIndividualSaleData_tb($year,$salecode,$type,$elig,$gait,$sort1,$sort2,$sort3,$sort4,$sort5)
{
    global $mysqli;
//     if ($year == "" && $salecode == "" && $type == "" && $elig == "" && $gait == "") {
//         return "";
//     }
    
    $searchParam = ' AND YEAR(Saledate)= IF("'.$year.'" = "", YEAR(Saledate), "'.$year.'")
                     AND Salecode= IF("'.$salecode.'"  = "", Salecode, "'.$salecode.'")
                     AND Type= IF("'.$type.'"  = "", Type, "'.$type.'")
                     AND Elig= IF("'.$elig.'"  = "", Elig, "'.$elig.'")
                     AND Gait= IF("'.$gait.'"  = "", Gait, "'.$gait.'") ';
    $sql =
    'SELECT ORank,Frank,CRank, HIP, Horse, Sex, Color, `Type`, Datefoal, Elig, Sire, Dam, Salecode, Consno, Saledate, `Day`,
        a.Price, Currency, Purlname, Purfname, Rating FROM (
        SELECT
        HIP,
        Horse,
        Sex,
        Color,
        a.`Type`,
        Datefoal,
        Elig,
        b.Sire,
        b.Dam,
        Salecode,
        Consno,
        Saledate,
        a.`Day`,
        Currency,
        Price,
        Purlname,
        Purfname,
        Rating
        FROM tsales a
        JOIN tdamsire b ON a.damsire_Id=b.damsire_ID
        WHERE PRICE>0 '.$searchParam.') a
	LEFT JOIN
    (SELECT Price AS Rankprice ,(@curRank := @curRank + 1) AS ORank from (
		SELECT Price FROM tsales a
		JOIN tdamsire b ON a.damsire_Id=b.damsire_ID WHERE PRICE>0 '.$searchParam.'
        group by Price ORDER BY Price desc) as a,(SELECT @curRank := 0) r) b
		on a.price=b.Rankprice
    LEFT JOIN
    (select price  AS P1,sex AS S1,(@curRank1 := @curRank1 + 1) AS FRank from (
             SELECT price, sex FROM tsales a
             JOIN tdamsire b ON a.damsire_Id=b.damsire_ID WHERE Sex IN ("F","M") AND PRICE>0 '.$searchParam.'
             group by price,sex ORDER BY price desc) as a,(SELECT @curRank1 := 0) r) c
             on a.price=c.P1 and a.Sex=c.S1
    LEFT JOIN
    (select price  AS P2,sex AS S2,(@curRank2 := @curRank2 + 1) AS CRank from (
             SELECT price, sex FROM tsales a
             JOIN tdamsire b ON a.damsire_Id=b.damsire_ID WHERE Sex IN ("C","H","G") AND PRICE>0 '.$searchParam.'
             group by price,sex ORDER BY price desc) as a,(SELECT @curRank2 := 0) r) d
             on a.price=d.P2 and a.Sex=d.S2';
    
    $orderby1 = ' ORDER BY '.$sort1;
    $orderby2 = ', '.$sort2;
    $orderby3 = ', '.$sort3;
    $orderby4 = ', '.$sort4;
    $orderby5 = ', '.$sort5;
    
    
    if ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !="" && $sort5 !="") {
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4.$orderby5;
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !="" && $sort4 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3.$orderby4;
    }elseif ($sort1 !="" && $sort2 !="" && $sort3 !=""){
        $sql = $sql.$orderby1.$orderby2.$orderby3;
    }elseif ($sort1 !="" && $sort2 !=""){
        $sql = $sql.$orderby1.$orderby2;
    }elseif ($sort1 !=""){
        $sql = $sql.$orderby1;
    }
    
    //echo $sql;
    $result = mysqli_query($mysqli, $sql);
    
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
        echo $sql;
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
    
}

function getDamsireID($csire,$cdam)
{
    global $mysqli;
    $sqlDamsireCheck = "SELECT damsire_ID FROM damsire WHERE csire='".$csire."' AND cdam='".$cdam."'";
    try {
        $result = $mysqli->query($sqlDamsireCheck);
        $damsire_ID = $result->fetch_assoc();
    } catch(Exception $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    return $damsire_ID['damsire_ID'];
}

function getTDamsireID($csire,$cdam)
{
    global $mysqli;
    $sqlDamsireCheck = "SELECT damsire_ID FROM tdamsire WHERE csire='".$csire."' AND cdam='".$cdam."'";
    try {
        $result = $mysqli->query($sqlDamsireCheck);
        $damsire_ID = $result->fetch_assoc();
    } catch(Exception $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    return $damsire_ID['damsire_ID'];
}

function getLastDamsireID()
{
    global $mysqli;
    $sqlDamsireId = "SELECT max(damsire_ID) as ID FROM damsire";
    $result = $mysqli->query($sqlDamsireId);
    try {
        $damsire_ID = $result->fetch_assoc();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    return $damsire_ID['ID'];
}

function getLastTDamsireID()
{
    global $mysqli;
    $sqlDamsireId = "SELECT max(damsire_ID) as ID FROM tdamsire";
    $result = $mysqli->query($sqlDamsireId);
    try {
        $damsire_ID = $result->fetch_assoc();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    return $damsire_ID['ID'];
}

function checkSalesData($tattoo,$hip,$chorse,$salecode,$saledate)
{
    global $mysqli;
    $sqlDamsireId = "SELECT SALEID FROM sales WHERE TATTOO='".$tattoo."' AND HIP='".$hip."' AND CHORSE='".$chorse."'
                     AND SALECODE='".$salecode."' AND SALEDATE='".$saledate."'";
    $result = $mysqli->query($sqlDamsireId);
    try {
        $salesExist = $result->fetch_assoc();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    return $salesExist['SALEID'];
}

function checkSalesforUpdate($hip,$salecode,$datefoal)
{
    global $mysqli;
    $sqlDamsireId = "SELECT SALEID FROM sales WHERE HIP='".$hip."'
                     AND SALECODE='".$salecode."' AND DATEFOAL='".$datefoal."'";
    $result = $mysqli->query($sqlDamsireId);
    try {
        $salesExist = $result->fetch_assoc();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    return $salesExist['SALEID'];
}

function checkSalesforETUpdate($tattoo,$salecode,$datefoal)
{
    global $mysqli;
    $sqlDamsireId = "SELECT SALEID FROM sales WHERE TATTOO='".$tattoo."'
                     AND SALECODE='".$salecode."' AND DATEFOAL='".$datefoal."'";
    $result = $mysqli->query($sqlDamsireId);
    try {
        $salesExist = $result->fetch_assoc();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    return $salesExist['SALEID'];
}

function checkTSalesData($tattoo,$hip,$chorse,$salecode,$saledate)
{
    global $mysqli;
    $sqlDamsireId = "SELECT SALEID FROM tsales WHERE TATTOO='".$tattoo."' AND HIP='".$hip."' AND CHORSE='".$chorse."'
                     AND SALECODE='".$salecode."' AND SALEDATE='".$saledate."'";
    $result = $mysqli->query($sqlDamsireId);
    try {
        $salesExist = $result->fetch_assoc();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    return $salesExist['SALEID'];
}

function getUserID($user)
{
    global $mysqli;
    $sql = "SELECT user_id as ID FROM users WHERE username = '".$user."'";
    $result = $mysqli->query($sql);
    try {
        $user_ID = $result->fetch_assoc();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    return $user_ID['ID'];
}

function fetchUserDetails($user)
{
    global $mysqli;
    $sql = "SELECT * FROM users WHERE username = '".$user."'";
    $result = $mysqli->query($sql);
    try {
        $user = $result->fetch_assoc();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    return $user;
}

function getUserData()
{
    global $mysqli;
    $sql = "SELECT USER_ID, USERNAME, FNAME, LNAME, ACTIVE, USERROLE FROM users";
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function unauthorizeUser($userId)
{
    global $mysqli;
    $sql = 'UPDATE users set ACTIVE="N" WHERE USER_ID = '.$userId;
    //echo $sql;
    if ($mysqli->query($sql) === TRUE) {
        $result = $userId." - User Unauthorized successfully";
    } else {
        $result = "Error updating record: " . $mysqli->error;
    }
    //echo "aaaaa";
    return $result;
}

function authorizeUser($userId)
{
    global $mysqli;
    $sql = 'UPDATE users set ACTIVE="Y" WHERE USER_ID = '.$userId;
    //echo $sql;
    if ($mysqli->query($sql) === TRUE) {
        $result = $userId." - User Authorized successfully";
    } else {
        $result = "Error updating record: " . $mysqli->error;
    }
    //echo "aaaaa";
    return $result;
}

function array_to_csv_download($array, $filename = "export.csv", $delimiter=";") {
//     ob_start();
//     // open raw memory as file so no temp files needed, you might run out of memory though
//     $f = fopen('php://memory', 'w');
    
//     // loop over the input array
//     foreach ($array as $line) {
//         // generate csv lines from the inner arrays
//         fputcsv($f, $line, $delimiter);
//     }
//     // reset the file pointer to the start of the file
//     fseek($f, 0);
//     // tell the browser it's going to be a csv file
//     //header('Content-Type:application/csv');
//     // tell the browser we want to save it instead of displaying it
//     //header('Content-Disposition:attachment; filename="'.$filename.'";');
//     // make php send the generated csv lines to the browser
//     header('Content-Type: text/csv; charset=utf-8');
//     header('Content-Disposition: attachment; filename=data.csv');
//     fpassthru($f);
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="sample.csv"');
    $data = array(
        'aaa,bbb,ccc,dddd',
        '123,456,789',
        '"aaa","bbb"'
    );
    
    $fp = fopen('php://output', 'wb');
    foreach ( $data as $line ) {
        $val = explode(",", $line);
        fputcsv($fp, $val);
    }
    fclose($fp);
}

function getsaledata($breed)
{
    if ($breed == "") {
        return [];
    }
    
    $orderBy = isset($_GET['orderby']) ? $_GET['orderby'] : 'Saledate';
    $sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'ASC';

    global $mysqli;
    $sql = "SELECT Salecode, Saledate, upload_date, count(*) FROM sales GROUP BY salecode ORDER BY $orderBy $sortOrder";
    if ($breed == "T") {
        $sql = "SELECT Salecode, Saledate, upload_date, count(*) FROM tsales GROUP BY salecode ORDER BY $orderBy $sortOrder";
    }


    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}


function deleteSalecode($breed,$salecode)
{
    global $mysqli;
    echo $breed;
    echo $salecode;
    $sql = 'DELETE FROM sales WHERE Salecode = "'.$salecode.'"';
    if ($breed == "T") {
        $sql = 'DELETE FROM tsales WHERE Salecode = "'.$salecode.'"';
    }
    //echo $sql;
    if ($mysqli->query($sql) === TRUE) {
        $result = "Record deleted successfully";
    } else {
        $result = "Error deleting record: " . $mysqli->error;
    }
    //echo "aaaaa";
    return $result;
}
?>
