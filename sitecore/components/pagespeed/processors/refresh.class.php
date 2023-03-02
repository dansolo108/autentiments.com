<?php
//
    class refreshProcessor extends modProcessor {
        public function checkPermissions() {
            return $this->modx->hasPermission( 'empty_cache' );
        }
        public function process() {
            $results = [];
            $this->modx->getCacheManager()->refresh( [ PageSpeed::class => [] ], $results );
            if( is_bool( $results[ PageSpeed::class ] ) )
                $this->modx->log( modX::LOG_LEVEL_INFO, $this->modx->lexicon( 'refresh_' . PageSpeed::class, [ 'partition' => PageSpeed::class ] ) . $this->modx->lexicon( 'refresh_' . ( $results[ PageSpeed::class ] ? 'success' : 'failure' ) ) );
            elseif( is_array( $results[ PageSpeed::class ] ) )
                $this->modx->log( modX::LOG_LEVEL_INFO, $this->modx->lexicon( 'refresh_' . PageSpeed::class, [ 'partition' => PageSpeed::class ] ) . print_r( $results[ PageSpeed::class ], TRUE ) );
            sleep( 1 );
            return $this->success();
        }
    }
    return refreshProcessor::class;
