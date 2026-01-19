import {
	readFileSync,
	writeFileSync,
	existsSync,
	mkdirSync,
} from 'fs';
import path from 'path';
// eslint-disable-next-line import/no-extraneous-dependencies
import browserslist from 'browserslist';
import { bundleAsync, browserslistToTargets } from 'lightningcss';
// eslint-disable-next-line import/no-extraneous-dependencies
import * as sass from 'sass';
import { paths } from './lib/constants.js';

// Determine if running in development mode
const isDev = process.argv.includes( '--dev' );

/**
 * Ensure output directory exists.
 * @param {string} dir
 * @return {void}
 */
const ensureDirectoryExistence = ( dir ) => {
	if ( ! existsSync( dir ) ) {
		mkdirSync( dir, { recursive: true } );
	}
};

ensureDirectoryExistence( paths.styles.dest );

/**
 * Process spacing system CSS file.
 * @return {Promise<void>}
 */
const processSpacingSystem = async () => {
	const scssFile = path.resolve( paths.styles.srcDir, 'spacing-system/_spacing-system.scss' );
	const tempCssFile = path.resolve( paths.styles.srcDir, 'spacing-system/_spacing-system.css' );
	const inputFile = path.resolve( paths.styles.srcDir, 'spacing-system.css' );
	const outputFile = path.join( paths.styles.dest, 'spacing-system.min.css' );

	if ( ! existsSync( scssFile ) ) {
		console.error( `[Spacing System] SCSS file not found: ${ scssFile }` );
		process.exit( 1 );
	}

	console.log( `[Spacing System] Compiling SCSS to CSS...` );
	
	// First, compile SCSS to CSS
	const scssResult = sass.compile( scssFile, {
		style: isDev ? 'expanded' : 'compressed',
		sourceMap: isDev,
	} );
	
	// Write temporary CSS file
	writeFileSync( tempCssFile, scssResult.css );
	if ( isDev && scssResult.sourceMap ) {
		writeFileSync( `${ tempCssFile }.map`, JSON.stringify( scssResult.sourceMap ) );
	}

	// Update spacing-system.css to import the compiled CSS
	const spacingSystemCss = `/*----------------------------
# Spacing System
----------------------------*/
@import "spacing-system/_spacing-system.css";
`;
	writeFileSync( inputFile, spacingSystemCss );

	// Resolve Browserslist targets
	const browserslistEnv =
		process.env.BROWSERSLIST_ENV ||
		( isDev ? 'development' : 'production' );
	const browsers = browserslist( null, {
		path: process.cwd(),
		env: browserslistEnv,
	} );
	const targets = browserslistToTargets( browsers );

	console.log( `[Spacing System] Processing with LightningCSS ${ isDev ? '(dev)' : '(production)' }...` );

	const result = await bundleAsync( {
		filename: inputFile,
		minify: ! isDev,
		sourceMap: isDev,
		sourceMapIncludeSources: true,
		drafts: { customMedia: true },
		targets,
		resolver: {
			read( readPath ) {
				return readFileSync( readPath, 'utf8' );
			},
			resolve( specifier, from ) {
				return path.resolve( path.dirname( from ), specifier );
			},
		},
	} );

	if ( result.map ) {
		const mapFile = `${ outputFile }.map`;
		writeFileSync( mapFile, result.map );
		const cssWithMap = Buffer.concat( [
			result.code,
			Buffer.from( `\n/*# sourceMappingURL=${ path.basename( mapFile ) } */\n` ),
		] );
		writeFileSync( outputFile, cssWithMap );
	} else {
		writeFileSync( outputFile, result.code );
	}

	console.log( `[Spacing System] Built successfully: ${ outputFile }` );
};

// Run build
( async () => {
	try {
		await processSpacingSystem();
	} catch ( error ) {
		console.error( '[Spacing System] Build failed:', error );
		process.exit( 1 );
	}
} )();

