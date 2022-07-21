<?php namespace SamagTech\CoreLumen\Middleware;

use Closure;
use SamagTech\CoreLumen\Models\System;

/**
 * Controlla se il server è in modalità manutenzione
 *
 * @author Alessandro Marotta <alessandro.marotta@samag.tech>
 *
 * @since v1.1
 */
class MaintenanceMiddleware {

    /**
     * Modello per la gestione delle configurazioni di sistema
     *
     * @access private
     *
     * @var System
     */
    private System $system;

    //-----------------------------------------------------------------------

    /**
     * Costruttore.
     *
     * @param System    $system Modello per le configurazioni di systema
     */
    public function __construct(System $system) {
        $this->system = $system;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        if ( $this->system->find('maintenance')?->value ) {
            return response()->json(['message' => __('response.maintenance_mode')], 503);
        }

        return $next($request);
    }
}
