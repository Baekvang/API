<?
    abstract class API {
        /**
         * Property: method
         * The HTTP method this request was made in, either GET or POST
         */
        protected $method = '';
        /**
         * Property: endpoint
         * The Model requested in the URI. eg: /files
         */
        protected $endpoint = '';
        /**
         * Property: verb
         * An optional additional descriptor about the endpoint, used for things that can
         * not be handled by the basic methods. eg: /files/process
         */
        protected $verb = '';
        /**
         * Property: args
         * Any additional URI components after the endpoint and verb have been removed, in our
         * case, an integer ID for the resource. eg: /<endpoint>/<verb>/<arg0>/<arg1>
         * or /<endpoint>/<arg0>
         */
        protected $args = Array();

        public function __construct($aRequest) {
            header("Access-Control-Allow-Orgin: *");
            header("Access-Control-Allow-Methods: *");
            header("Content-Type: application/json");

            $this->args = $aRequest;
            $this->endpoint = array_shift($this->args);
            if(array_key_exists(0, $this->args) && !is_numeric($this->args[0])) {
                $this->verb = array_shift($this->args);
            }

            $this->method = $_SERVER['REQUEST_METHOD'];

            switch($this->method) {
                case 'POST':
                    $this->request = $this->_cleanInputs($_POST);
                    break;
                case 'GET':
                    $this->request = $this->_cleanInputs($_GET);
                    break;
                default:
                    $this->_response('Invalid Method', 405);
                    break;
            }
            var_dump($this->request);
            $this->verifyKey($this->request['apiKey']);
            $this->processAPI();
        }

         public function processAPI() {
            if ((int)method_exists($this, $this->endpoint) > 0) {
                return $this->_response($this->{$this->endpoint}($this->args));
            }
            return $this->_response("No Endpoint: $this->endpoint", 404);
        }

        private function _response($aData, $iStatus = 200) {
            header("HTTP/1.1 " . $iStatus . " " . $this->_requestStatus($iStatus));
            
            return json_encode($aData);
        }

        private function _cleanInputs($aData) {
            $clean_input = Array();
            if (is_array($aData)) {
                foreach ($aData as $k => $v) {
                    $clean_input[$k] = $this->_cleanInputs($v);
                }
            } else {
                $clean_input = trim(strip_tags($aData));
            }
            return $clean_input;
        }

        private function _requestStatus($iCode) {
            $aStatus = array(  
                200 => 'OK',
                404 => 'Not Found',   
                405 => 'Method Not Allowed',
                500 => 'Internal Server Error',
            ); 
            if(!empty($aStatus[$iCode])) {
                return $aStatus[$iCode];
            } else {
                return $aStatus[500];
            }
        }

        private function verifyKey($sKey) {
            $aAllowedKeys = array('1rJzHk8UaH0CT61YqL9Jo0F5k2Ey1Xx2'); // API key
            if (!array_key_exists('apiKey', $this->request)) {
                throw new Exception('No API Key provided');
            } else if(!in_array($sKey, $aAllowedKeys)) {
                throw new Exception('Invalid API Key');
            } else {
                return true;
            }
        }      
    }
?>