import { readFileSync, writeFileSync, existsSync, mkdirSync } from 'fs';
import path from 'path';
// eslint-disable-next-line import/no-extraneous-dependencies
import browserslist from 'browserslist';
import { bundleAsync, browserslistToTargets } from 'lightningcss';
import { paths } from './lib/constants.js';

// Determine if running in development mode
const isDev = process.argv.includes('--dev');

/**
 * Ensure output directory exists.
 * @param {string} dir
 * @return {void}
 */
const ensureDirectoryExistence = (dir) => {
	if (!existsSync(dir)) {
		mkdirSync(dir, { recursive: true });
	}
};

ensureDirectoryExistence(paths.styles.dest);

/**
 * Process typography system CSS file.
 * @return {Promise<void>}
 */
const processTypographySystem = async () => {
	const inputFile = path.resolve(
		paths.styles.srcDir,
		'typography/_typography-system.css'
	);
	const outputFile = path.join(paths.styles.dest, 'typography-system.min.css');

	if (!existsSync(inputFile)) {
		console.error(`[Typography System] Input file not found: ${inputFile}`);
		process.exit(1);
	}

	// Resolve Browserslist targets
	const browserslistEnv =
		process.env.BROWSERSLIST_ENV || (isDev ? 'development' : 'production');
	const browsers = browserslist(null, {
		path: process.cwd(),
		env: browserslistEnv,
	});
	const targets = browserslistToTargets(browsers);

	console.log(
		`[Typography System] Building ${isDev ? '(dev)' : '(production)'}...`
	);

	const result = await bundleAsync({
		filename: inputFile,
		minify: !isDev,
		sourceMap: isDev,
		sourceMapIncludeSources: true,
		drafts: { customMedia: true },
		targets,
		resolver: {
			read(readPath) {
				return readFileSync(readPath, 'utf8');
			},
			resolve(specifier, from) {
				return path.resolve(path.dirname(from), specifier);
			},
		},
	});

	if (result.map) {
		const mapFile = `${outputFile}.map`;
		writeFileSync(mapFile, result.map);
		const cssWithMap = Buffer.concat([
			result.code,
			Buffer.from(`\n/*# sourceMappingURL=${path.basename(mapFile)} */\n`),
		]);
		writeFileSync(outputFile, cssWithMap);
	} else {
		writeFileSync(outputFile, result.code);
	}

	console.log(`[Typography System] Built successfully: ${outputFile}`);
};

// Run build
(async () => {
	try {
		await processTypographySystem();
	} catch (error) {
		console.error('[Typography System] Build failed:', error);
		process.exit(1);
	}
})();
