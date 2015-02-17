<?php



class patientController extends baseController {

    public function __construct() {
        parent::__construct();
        $this->model="patientModel";
        $this->view = "patientsView.twig";
    }


    public function getPatients($column, $order, $offset, $length, $ajax){
        $data = $this->getData($column, $order, $offset, $length, $this->model);

        foreach ($data['data'] as $key =>$value){
            $rel = $data['data'][$key]['relationship'];
            switch ($rel) {
                case "01":
                    $data['data'][$key]['relationship'] = "proband";
                    break;
                case "02":
                    $data['data'][$key]['relationship'] = "mother";
                    break;
                case "03":
                    $data['data'][$key]['relationship'] = "father";
                    break;
                case "04":
                    $data['data'][$key]['relationship'] = "sibling";
                    break;
                case "05":
                    $data['data'][$key]['relationship'] = "sibling";
                    break;
                case "06":
                    $data['data'][$key]['relationship'] = "sibling";
                    break;
                case "07":
                    $data['data'][$key]['relationship'] = "other";
                    break;
            }

            if (count($data['data'][$key]['medicalChartId']) > 1) $data['data'][$key]['patientId'] = $data['data'][$key]['medicalChartId'];
            else {
                $dob = new Datetime($data['data'][$key]["dateOfBirth"]);
                $data['data'][$key]['patientId'] = $data['data'][$key]["lastName"][0].$data['data'][$key]["firstName"][0].$dob->format("ymd") ;
            }

            $created = new DateTime($data['data'][$key]['created']);
            $data['data'][$key]['familyId'] = "F".$created->format("y").$data['data'][$key]['familyId'];
            $data['data'][$key]['familyIndividualId'] =  $data['data'][$key]['familyId']."-" . $rel;
        }


        if($ajax == 1 || $ajax == "1"){
            return json_encode($data);
        }
        else {
            $this->app->render($this->view, array("data"=>$data));
        }

    }

    public function fetchPatient($column, $order, $offset, $length){
        return $this->getData($column, $order, $offset, $length, 0, $this->model);
    }

    public function deletePatient(){

    }

    public function addPatient(){

    }

    public function editPatient($data){
        return $this->editCell($data,$this->model);
    }


    public function processExcel($data){
        $this->processExcel_($data, $this->model, "/demographicsDB/patients", "fixPatient.twig");
    }

    public function processAjax($data){
        return $this->processAjax_($data, $this->model);
    }

}