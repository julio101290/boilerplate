<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\{CentrocostosModel};
use CodeIgniter\API\ResponseTrait;
use julio101290\boilerplatelog\Models\LogModel;
use julio101290\boilerplatecompanies\Models\EmpresasModel;

class CentrocostosController extends BaseController
{
    use ResponseTrait;

    protected $log;
    protected $centrocostos;
    protected $empresa;

    public function __construct()
    {
        $this->centrocostos = new CentrocostosModel();
        $this->log = new LogModel();
        $this->empresa = new EmpresasModel();
        helper(['menu', 'utilerias']);
    }

    public function index()
    {
        helper('auth');

        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);
        $empresasID = count($titulos["empresas"]) === 0 ? [0] : array_column($titulos["empresas"], "id");

        if ($this->request->isAJAX()) {
            $request = service('request');

            $draw = (int) $request->getGet('draw');
            $start = (int) $request->getGet('start');
            $length = (int) $request->getGet('length');
            $searchValue = $request->getGet('search')['value'] ?? '';
            $orderColumnIndex = (int) $request->getGet('order')[0]['column'] ?? 0;
            $orderDir = $request->getGet('order')[0]['dir'] ?? 'asc';

            $fields = $this->centrocostos->allowedFields;
            $orderField = $fields[$orderColumnIndex] ?? 'id';

            $builder = $this->centrocostos->mdlGetCentrocostos($empresasID);

            $total = clone $builder;
            $recordsTotal = $total->countAllResults(false);

            if (!empty($searchValue)) {
                $builder->groupStart();
                foreach ($fields as $field) {
                    $builder->orLike("a." . $field, $searchValue);
                }
                $builder->groupEnd();
            }

            $filteredBuilder = clone $builder;
            $recordsFiltered = $filteredBuilder->countAllResults(false);

            $data = $builder->orderBy("a." . $orderField, $orderDir)
                             ->get($length, $start)
                             ->getResultArray();

            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ]);
        }

        $titulos["title"] = lang('centrocostos.title');
        $titulos["subtitle"] = lang('centrocostos.subtitle');
        return view('centrocostos', $titulos);
    }

    public function getCentrocostos()
    {
        helper('auth');

        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);
        $empresasID = count($titulos["empresas"]) === 0 ? [0] : array_column($titulos["empresas"], "id");

        $idCentrocostos = $this->request->getPost("idCentrocostos");
        $dato = $this->centrocostos->whereIn('idEmpresa', $empresasID)
                                   ->where('id', $idCentrocostos)
                                   ->first();

        return $this->response->setJSON($dato);
    }

    public function save()
    {
        helper('auth');

        $userName = user()->username;
        $datos = $this->request->getPost();
        $idKey = $datos["idCentrocostos"] ?? 0;

        if ($idKey == 0) {
            try {
                if (!$this->centrocostos->save($datos)) {
                    $errores = implode(" ", $this->centrocostos->errors());
                    return $this->respond(['status' => 400, 'message' => $errores], 400);
                }
                $this->log->save([
                    "description" => lang("centrocostos.logDescription") . json_encode($datos),
                    "user" => $userName
                ]);
                return $this->respond(['status' => 201, 'message' => 'Guardado correctamente'], 201);
            } catch (\Throwable $ex) {
                return $this->respond(['status' => 500, 'message' => 'Error al guardar: ' . $ex->getMessage()], 500);
            }
        } else {
            if (!$this->centrocostos->update($idKey, $datos)) {
                $errores = implode(" ", $this->centrocostos->errors());
                return $this->respond(['status' => 400, 'message' => $errores], 400);
            }
            $this->log->save([
                "description" => lang("centrocostos.logUpdated") . json_encode($datos),
                "user" => $userName
            ]);
            return $this->respond(['status' => 200, 'message' => 'Actualizado correctamente'], 200);
        }
    }

    public function delete($id)
    {
        helper('auth');

        $userName = user()->username;
        $registro = $this->centrocostos->find($id);

        if (!$this->centrocostos->delete($id)) {
            return $this->respond(['status' => 404, 'message' => lang("centrocostos.msg.msg_get_fail")], 404);
        }

        $this->centrocostos->purgeDeleted();
        $this->log->save([
            "description" => lang("centrocostos.logDeleted") . json_encode($registro),
            "user" => $userName
        ]);

        return $this->respondDeleted($registro, lang("centrocostos.msg_delete"));
    }
}