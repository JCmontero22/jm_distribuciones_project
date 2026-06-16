<?php
require_once('../config/config.php');

    require_once('../core/ServiceContainer.php');
    require_once('../controller/FormulasController.php');

    $accion = isset($_POST['accion']) ? $_POST['accion'] :  (isset($_GET['accion']) ? $_GET['accion'] : null);

    $servicio = ServiceContainer::getFormulaService();
    $formulaController = new FormulasController($servicio);
    $catalogoService = ServiceContainer::getCatalogoService();

    // Pasar la categoría en la request
    $request = array_merge($_GET, $_POST);
    $request['categoria'] = $_POST['categoriaProducto'] ?? $_GET['categoria'] ?? null;

    switch ($accion) {
        case 'registrarFormula':
            $resultado = $formulaController->registrar($_POST);
            echo json_encode($resultado);
            break;

        case 'listarFormulas':
            $resultado = $formulaController->listar($request);
            echo json_encode($resultado);
            break;

        case 'listarPresentaciones':
            $request['categoria'] = $_GET['categoria'] ?? $_POST['categoria'] ?? null;
            $resultado = $formulaController->listarPresentaciones($request);
            echo json_encode($resultado);
            break;

        case 'listarInsumos':
            $resultado = $catalogoService->obtenerInsumosFormulas();
            echo json_encode($resultado);
            break;

        case 'listarConcentraciones':
            $resultado = $catalogoService->obtenerTiposConcentracion();
            echo json_encode($resultado);
            break;
        
        case 'obtenerFormula':
            $idFormula = $_GET['id_formula'] ?? null;
            $resultado = $formulaController->obtenerFormulaPorID($idFormula);
            echo json_encode($resultado);
            break;

        case 'editarFormula':
            $resultado = $formulaController->editarFormula($_POST);
            echo json_encode($resultado);
            break;

        case 'eliminarFormula':
            $resultado = $formulaController->eliminarFormula($_POST['id_formula'] ?? null);
            echo json_encode($resultado);
            break;

        default:
            echo json_encode(['error' => 'Acción no válida']);
            break;
    }
