<?php namespace SamagTech\CoreLumen\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Trait utilizzato per lanciare eventi post azioni del crud
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 *
 * @since v1.4.0
 */
trait HasCrudEvents {

    /**
     * Array contentente i namespace degli eventi da lanciare.
     *
     * Deve essere costruito con chiave la funzione che triggera l'evento
     * e con valore la lista degli eventi da lanciare.
     *
     * @access protected
     *
     * Es.
     * [
     *      'store' => [
     *          Event::class,
     *          Event2::class
     *      ]
     * ]
     *
     * @var array
     */
    protected array $crudEvents = [];

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public function store (Request $request) : JsonResource|array {

        $resource = parent::store($request);

        $this->firedEvents('store', $resource);

        return $resource;
    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public function update (Request $request, int | string $id) : bool {

        $updated = parent::update($request, $id);

        $this->firedEvents('update', ['id' => $id]);

        return $updated;
    }

    //-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public function delete (int|string $id) : bool {

        $deleted = parent::delete($id);

        $this->firedEvents('delete', ['id' => $id]);

        return $deleted;

    }

    //-----------------------------------------------------------------------

    /**
     * Funzione che lancia l'evento
     *
     * @access private
     *
     * @param string $action    Azione che triggera l'evento
     * @param array $data       Eventuali dati
     *
     * @return void
     */
    private function firedEvents(string $action, $data = []) : void {

        if ( ! isset($this->crudEvents[$action]) ) return;

        foreach ( $this->crudEvents[$action] as $event) {
            event(new $event($data));
        }
    }

    //-----------------------------------------------------------------------
}