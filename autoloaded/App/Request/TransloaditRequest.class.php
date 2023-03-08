<?php

namespace App\Request;

class TransloaditRequest {
    // Attributs statiques.
    private const ENDPOINT = "https://api2.transloadit.com";
    private const PATH = "/assemblies";
    private const EXPIRES = "+2 hours";

    public const PDF_THUMBNAIL = "fac66967b81b4a04b534c96d23d9aa2b";

    // Constructeur statique.
    public static function new(array $files = array()): TransloaditRequest {
        return new TransloaditRequest($files);
    }

    // Attributs.
    private array $files;
    private string $assemblyId;

    // Constructeur
    private function __construct(array $files) {
        $this->files = $files;
    }

    private function request(string $templateId): object {
        $ping = isset($this->assemblyId);

        if (!$ping) $params["template_id"] = $templateId;

        if (!ini_get('date.timezone')) date_default_timezone_set('Etc/UTC');

        $params['auth'] = array(
            'key' => getenv("SECRET_TRANSLOADIT_KEY"),
            'expires' => gmdate('Y/m/d H:i:s+00:00', strtotime(TransloaditRequest::EXPIRES)),
        );

        $fields['params'] = json_encode($params);

        $signature = hash_hmac('sha1', $fields['params'], getenv("SECRET_TRANSLOADIT_SECRET"));

        if (!empty($signature)) {
            $fields['signature'] = $signature;
        }

        return $this->curlExecute(
            TransloaditRequest::ENDPOINT . TransloaditRequest::PATH . ($ping ? "/$this->assemblyId" : ""),
            $ping ? "GET" : "POST",
            $fields,
            $ping ? array() : $this->files
        );
    }

    private function curlExecute(string $url, string $method, array $fields, array $files): object {
        // Initialisation d'un CurlHandle
        $curlHandle = curl_init();

        // Initialisation des options.
        $options = array();

        // Si la méthode utilisée est POST, alors il peut y avoir des fichiers que l'on stocke dans le Body.
        if ($method === 'POST') {
            // Pour chaque fichier indicé dans les fichiers transmis.
            foreach ($files as $index => $file) {
                // Si le fichier n'existe pas on soulève une erreur (impossible de continuer).
                if (!file_exists($file)) trigger_error("File $file does not exist", E_USER_ERROR);

                // S'il existe bien on crée une instance CURLFile du fichier que l'on stocke comme champ "file_x".
                $fields["file_" . ($index + 1)] = curl_file_create($file);
            }

            // On ajoute l'option CURLOPT_POSTFIELDS correspondant aux champs que l'on vient de compléter.
            $options[CURLOPT_POSTFIELDS] = $fields;
        }

        // Si la méthode n'est pas POST, alors on stocke les champs dans l'URL.
        else {
            $url .= '?' . http_build_query($fields);
        }

        // Setting du tableau des options.
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_CUSTOMREQUEST] = $method;
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

        // Retour de la réponse.
        return $response;
    }

    public function executeTemplate(string $templateId): array {
        $response = $this->request($templateId);

        $this->assemblyId = explode("/", parse_url($response->data['assembly_ssl_url'])['path'])[2];

        while ($response->data["ok"] == 'ASSEMBLY_UPLOADING' || $response->data['ok'] == 'ASSEMBLY_EXECUTING') {
            sleep(1);
            $response = $this->request($templateId);
        }

        return $this->retrieveResults($response->data);
    }

    private function retrieveResults(array $data): array {
        $resultsUrls = array();

        foreach ($data["results"] as $step => $results) {
            if ($step == ":output") {
                foreach ($this->files as $originalFile) {
                    $originalFileExploded = explode("/", $originalFile);
                    foreach ($results as $result) {
                        if ($result["original_name"] == end($originalFileExploded)) {
                            $resultsUrls[] = $result["ssl_url"];
                        }
                    }
                }
            }
        }

        return $resultsUrls;
    }
}
