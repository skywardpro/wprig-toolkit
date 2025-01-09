/* eslint-env es6 */
'use strict';

/**
 * External dependencies
 */
import { src, dest } from 'gulp';
import pump from 'pump';
import svgstore from 'gulp-svgstore';
import svgmin from 'gulp-svgmin';
import rename from 'gulp-rename';

/**
 * Internal dependencies
 */
import { paths } from './constants';

/**
 * Optimize images.
 * @param {function} done function to call when async processes finish
 * @return {Stream} single stream
 */
export default function generateSvgSprite( done ) {
	console.log( 'Starting generateSvgSprite...' );
	console.log( 'SVG Source Path:', paths.iconsSvg.src );
	console.log( 'SVG Destination Path:', paths.iconsSvg.dest );

	return pump(
		[
			src( paths.iconsSvg.src ),
			svgmin( {
				plugins: [
					{
						name: 'preset-default',
						params: {
							overrides: {
								removeTitle: false,
								removeDesc: false,
								removeAttrs: {
									attrs: 'class',
								},
								removeStyleElement: true,
								addAttributesToSVGElement: {
									attributes: [ 'aria-hidden', 'true' ],
								},
								convertColors: {
									names2hex: true,
									rgb2hex: true,
								},
							},
						},
					},
				],
			} ),
			svgstore(),
			rename( { basename: 'sprite' } ),
			dest( paths.iconsSvg.dest ),
		],
		( err ) => {
			if ( err ) {
				console.error( 'Error during generateSvgSprite:', err );
			} else {
				console.log( 'generateSvgSprite completed successfully.' );
			}
			done( err );
		}
	);
}
