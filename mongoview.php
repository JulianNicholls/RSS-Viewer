<?php
    require_once "sites.php";

    $conn = new Sites();

    echo "insert('blah1', 'http://blah1.com/rss.xml'): ";
    print_r($conn->insert(array('name' => 'blah1', 'url' => 'http://blah1.com/rss.xml')));

    echo "\ninsert('blah2', 'http://blah2.com/rss.xml'): ";
    print_r($conn->insert(array('name' => 'blah2', 'url' => 'http://blah2.com/rss.xml')));

    $first_id = show_all();

    try {
        echo "\nfind_by_id('invalid'):  ";
        echo $conn->find_by_id('invalid') === FALSE ? "FALSE (OK)" : $conn->find_by_id('invalid');
    } catch(Exception $e) {
        echo "Exception Caught (OK) - " . $e->getMessage();
    }

    echo "\n\nfind_by_id('valid but not present: 52276fc44c96e60c6b928bf2'):  ";
    echo $conn->find_by_id('52276fc44c96e60c6b928bf2') === FALSE ? "FALSE (OK)" : $conn->find_by_id('52276fc44c96e60c6b928bf2');

    echo "\n\nfind_by_id($first_id):  ";
    print_r($conn->find_by_id($first_id));

    echo "\n\nfind_by_name('invalid'):  ";
    echo $conn->find_by_name('invalid') == FALSE ? "FALSE (OK)" : $conn->find_by_name('invalid');

    echo "\n\nfind_by_name('Smashing Magazine'):  ";
    print_r($conn->find_by_name('Smashing Magazine'));

    $b1 = $conn->find_by_name('blah1');

    echo "\n\nupdate(blah1_id, blah3 etc): ";
    print_r($conn->update($b1['_id'], array('name' => 'blah3', 'url' => 'http://blah3.com/rss.xml')));

    $b3 = $conn->find_by_name('blah3');

    if($b3['_id'] == $b1['_id'])
        echo "\nupdate() retains the ID: '" . $b1['_id'] . "' == '" . $b3['_id'] . "'";
    else
        echo "\n*** update() changes the ID. ***";

    echo "\n\nBlah3: ";
    print_r($b3);

    echo "\n\nremove_by_id(blah3_id): ";
    print_r($conn->remove_by_id($b3['_id']));

    echo "\n\nremove_by_name('blah2'): ";
    print_r($conn->remove_by_name('blah2'));

    show_all();

// Show all of the stored sites and return the ID orf the first one

function show_all()
{
    global $conn;

    echo "\n\nall():\n";

    $data = $conn->all();

    foreach($data as $cur)
    {
        printf("  %-26s%-30s  %s\n", $cur['_id'], $cur['name'], $cur['url']);
    }

    return $data[0]['_id'];
}
