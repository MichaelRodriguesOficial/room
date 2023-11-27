<?php

/**
 * To open form for room object.
 */

$NEEDED_ITEMS = [
    'reservation',
    'plugin',
];

include '../../../inc/includes.php';

if (!isset($_GET['id'])) {
    $_GET['id'] = '';
}

if (!isset($_GET['withtemplate'])) {
    $_GET['withtemplate'] = '';
}

$room = new PluginRoomRoom();

if (isset($_POST['add'])) { // Adição de uma sala
    $room->check(-1, CREATE, $_POST);

    $newID = $room->add($_POST);
    Html::back();
} elseif (isset($_POST['delete'])) { // Exclusão de uma sala
    $room->check($_POST['id'], DELETE);

    $room->delete($_POST);
    Html::redirect($CFG_GLPI['root_doc'] . '/plugins/room/index.php');
} elseif (isset($_POST['purge'])) { // Purge de uma sala
    $room->check($_POST['id'], PURGE);

    $room->delete($_POST, 1);
    Html::redirect($CFG_GLPI['root_doc'] . '/plugins/room/index.php');
} elseif (isset($_POST['restore'])) { // Restauração de uma sala
    $room->check($_POST['id'], PURGE);

    $room->restore($_POST);
    Html::redirect($CFG_GLPI['root_doc'] . '/plugins/room/index.php');
} elseif (isset($_POST['update'])) { // Atualização de uma sala
    $room->check($_POST['id'], UPDATE);

    $room->update($_POST);
    Html::back();
} elseif (isset($_POST['additem'])) { // Adição de ligação a um computador
    $room->check($_POST['room_id'], UPDATE); // Deveria ser 'rooms_id'?

    if ($_POST['room_id'] > 0 && $_POST['computers_id'] > 0) {
        $room->plugin_room_AddDevice($_POST['room_id'], $_POST['computers_id']);
    }
    Html::back();
} elseif (isset($_POST['deleteitem'])) { // Remoção de ligação a um computador
    $room->check($_POST['room_id'], UPDATE);

    if (count($_POST['item'])) {
        foreach ($_POST['item'] as $key => $val) {
            $room->plugin_room_DeleteDevice($key);
        }
    }
    Html::back();
} else { // Visualização de uma sala
    $room->check($_GET['id'], READ);

    // Teste para definir a guia inicial a ser exibida ao abrir o registro
    $_SESSION['glpi_tab'] = $_SESSION['glpi_tab'] ?? 1;
    if (isset($_GET['tab'])) {
        $_SESSION['glpi_tab'] = $_GET['tab'];
    }

    Html::header(__('Room Management', 'room'), '', 'assets', 'pluginroommenu');

    $room->display($_GET);

    Html::footer();
}
