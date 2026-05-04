#!/usr/bin/env node
/**
 * Exit non-zero if the current Node major is below package.json engines.node (simple >=N check).
 */
const fs = require( 'fs' );
const path = require( 'path' );

const pkgPath = path.join( __dirname, '..', 'package.json' );
const pkg = JSON.parse( fs.readFileSync( pkgPath, 'utf8' ) );
const engines = pkg.engines && pkg.engines.node;
if ( ! engines ) {
	process.exit( 0 );
}

const match = String( engines ).match( />=\s*(\d+)/ );
if ( ! match ) {
	process.exit( 0 );
}

const needMajor = parseInt( match[ 1 ], 10 );
const haveMajor = parseInt( process.versions.node.split( '.' )[ 0 ], 10 );

if ( haveMajor < needMajor ) {
	process.stderr.write(
		`This project requires Node ${ needMajor } or newer (you have ${ process.version }).\n`
	);
	process.exit( 1 );
}
