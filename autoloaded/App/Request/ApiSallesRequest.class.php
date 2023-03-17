<?php

namespace App\Request;

use App\Message;
use Ds\Set;

class ApiSallesRequest {
    // Attributs statiques.
    private const ENDPOINT = "https://api.bde-tribu-terre.fr/v2.0/university";

    // Constructeur statique.
    public static function new(): ApiSallesRequest {
        return new ApiSallesRequest();
    }

    // Attributs
    // -- Aucuns --

    // Constructeur
    private function __construct() {}

    // Méthodes
    public function requestById(int $id): object {
        $room = $this->find("/room/", array(
            "id" => $id
        ))[0];

        if (!empty($room)) {
            $room->room_group_name = $this->find("/roomGroup/", array("id" => $room->room_group_id))[0]->name;
            $room->building_name = $this->find("/building/", array("id" => $room->building_id))[0]->long_label;
            $room->building_group_name = $this->find("/buildingGroup/", array("id" => $room->building_group_id))[0]->name;
        }

        return $room;
    }

    public function requestByName(string $name): array {
        // Recherche des salles correspondant au nom
        $rooms = $this->find("/room/", array(
            "name" => $name
        ));

        if (!empty($rooms)) {
            // Collecte des ID à rechercher ensuite
            $roomGroupsToFindById = array();
            $buildingsToFindById = array();
            $buildingGroupsToFindById = array();

            foreach ($rooms as $room) {
                if (!in_array($room->room_group_id, $roomGroupsToFindById)) {
                    $roomGroupsToFindById[] = $room->room_group_id;
                }

                if (!in_array($room->building_id, $buildingsToFindById)) {
                    $buildingsToFindById[] = $room->building_id;
                }

                if (!in_array($room->building_group_id, $buildingGroupsToFindById)) {
                    $buildingGroupsToFindById[] = $room->building_group_id;
                }
            }

            // Recherche des infos via les ID collectés
            $roomGroupsIdToName = array();
            $buildingsIdToName = array();
            $buildingGroupsIdToName = array();

            foreach ($this->find("/roomGroup/", array("id" => implode(",", $roomGroupsToFindById))) as $roomGroup) {
                $roomGroupsIdToName[$roomGroup->room_group_id] = $roomGroup->name;
            }

            foreach ($this->find("/building/", array("id" => implode(",", $buildingsToFindById))) as $building) {
                $buildingsIdToName[$building->building_id] = $building->long_label;
            }

            foreach ($this->find("/buildingGroup/", array("id" => implode(",", $buildingGroupsToFindById))) as $buildingGroup) {
                $buildingGroupsIdToName[$buildingGroup->building_group_id] = $buildingGroup->name;
            }

            // Rajout de toutes les infos aux salles
            foreach ($rooms as $room) {
                $room->room_group_name = $roomGroupsIdToName[$room->room_group_id];
                $room->building_name = $buildingsIdToName[$room->building_id];
                $room->building_group_name = $buildingGroupsIdToName[$room->building_group_id];
            }
        }

        return $rooms;
    }

    private function find(string $path, array $getFields): array {
        // Initialisation d'un CurlHandle.
        $curlHandle = curl_init();

        // Initialisation des options.
        $options = array();

        // Construction de l'URL.
        $url = self::ENDPOINT . $path . '?' . http_build_query($getFields);

        // Setting du tableau des options.
        $options[CURLOPT_RETURNTRANSFER] = true;
        // $options[CURLOPT_CUSTOMREQUEST] = "POST"; À n'utiliser que lorsque ce n'est ni GET ni HEAD.
        $options[CURLOPT_URL] = $url; // Contenant les champs si la méthode n'est pas POST.
        $options[CURLOPT_HTTPHEADER] = array();
        // $options[CURLOPT_POSTFIELDS] contient les champs dont les fichiers si la méthode est POST.

        // Mise en place du tableau des options dans le CurlHandle.
        curl_setopt_array($curlHandle, $options);

        // Création de l'objet réponse.
        $response = (object) [
            "data" => curl_exec($curlHandle),
            "curlInfo" => curl_getinfo($curlHandle),
            "curlErrorNumber" => curl_errno($curlHandle),
            "curlErrorMessage" => curl_error($curlHandle)
        ];

        // Fermeture du CurlHandle
        curl_close($curlHandle);

        // Décodage du JSON.
        $response->data = json_decode($response->data, true);

        // Vérification de la réponse.
        if (
            $response->curlInfo["http_code"] != 200 &&
            !isset($response->data, $response->data["data"], $response->data["data"]["objects"])
        ) {
            Message::add("Une erreur s'est produite suite à la requête auprès de l'API de requête de salles.");
            return array();
        }

        // Retour de la réponse.
        for ($i = 0; $i < count($response->data["data"]["objects"]); $i++) {
            $response->data["data"]["objects"][$i] = (object) $response->data["data"]["objects"][$i];
        }

        return $response->data["data"]["objects"];
    }


}