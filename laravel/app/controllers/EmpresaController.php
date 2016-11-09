<?php

class EmpresaController extends BaseController
{
    /**
     * muestra todos las empresas
     *
     * @return Response
     * uestra todos las empresas
     */
    public function index()
    {
        if (Input::has('sort')) {
            list($sortCol, $sortDir) = explode('|', Input::get('sort'));
            $query = Empresa::orderBy($sortCol, $sortDir);
        } else {
            $query = Empresa::orderBy('id', 'asc');
        }

        if (Input::has('filter')) {
            $filter=Input::get('filter');
            $query->where(function($q) use($filter) {
                $value = "%{$filter}%";
                $q->where('razon_social', 'like', $value)
                    ->orWhere('nombre_comercial', 'like', $value)
                    ->orWhere('direccion_fiscal', 'like', $value)
                    ->orWhere('ruc', 'like', $value);
            });
        }
        if (Input::has('usuario_actual')) {
            $query->misEmpresas();//('persona_id','=',Auth::id());
        }

        $perPage = Input::has('per_page') ? (int) Input::get('per_page') : null;

        return Response::json($query->paginate($perPage));
    }
    /**
     * muestra combo para crear
     *
     * @return Response
     */
    public function create() {
        return Persona::orderBy('display_name', 'asc')->lists('display_name', 'id');
    }
    /**
     * crear empresa
     *
     * @return Response
     */
    public function store()
    {
        $validator = Validator::make(Input::all(),Empresa::$rules, Persona::$messajes);
        if ( $validator->fails() ) {
            return Response::json([
                'rst'=>2,
                'msj'=>$validator->messages(),
            ]);
        }
        $empresa = Empresa::create(Input::all());

        $empresa->personas()->save(Auth::user(),
                [
                'cargo'=>Input::get('cargo'),
                'fecha_vigencia'=> Input::get('fecha_vigencia'),
                'persona_id'=> Auth::id(),
                'representante_legal'=> 1,//por defecto
                'estado'=> 1,
                'usuario_created_at'=> Auth::id()
                ]
        );
        return $empresa;
    }
    /**
     * muestra datos del recurso y combos asociados para editar
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        
        $empresa = Empresa::findOrFail($id);
        $empresaPersona = Empresa::find($id)->personas()->lists('representante_legal')->toArray();
        $persona = Persona::orderBy('display_name', 'asc')->lists('display_name', 'id');
        return Response::json(['empresa'=>$empresa,'empresa_persona'=>$empresaPersona,'persona'=>$persona]);
    
    }
    /**
     * solo muestra datos del recurso
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return Empresa::findOrFail($id);
    }
    /**
     * actualizar empresa
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $rules = Empresa::$rules;
        $rules['ruc']=$rules['ruc'].','.Input::get('id');
        $validator = Validator::make(Input::all(),$rules, Persona::$messajes);
        if ( $validator->fails() ) {
            return Response::json([
                'rst'=>2,
                'msj'=>$validator->messages(),
            ]);
        }
        Empresa::findOrFail($id)->update(Input::all());
        return Response::json(Input::all());
    }
    /**
     * eliminar empresa
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        return Empresa::destroy($id);
    }

}
