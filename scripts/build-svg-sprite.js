#!/usr/bin/env node
/* eslint-env es6 */
/**
 * Build SVG Sprite from icons
 * Generates a sprite.svg file from all SVG icons in assets/images/src/icons
 */

import { readFileSync, writeFileSync, existsSync, mkdirSync } from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import svgstore from 'svgstore';
import fg from 'fast-glob';
import { optimize as svgoOptimize } from 'svgo';
import { paths, rootPath } from './lib/constants.js';

// Check if running in dev mode
const isDev = process.argv.includes('--dev');

// Paths - use images dest path which handles production correctly
const iconsSrcDir = path.resolve(rootPath, 'assets', 'images', 'src', 'icons');
// Use images dest path which already handles production mode
const spriteDestDir = path.resolve(paths.images.dest, 'icons', 'sprite-svg');
const spriteDestFile = path.resolve(spriteDestDir, 'sprite.svg');

/**
 * Ensure directory exists
 */
function ensureDirectoryExistence(dirPath) {
	if (!existsSync(dirPath)) {
		mkdirSync(dirPath, { recursive: true });
	}
}

/**
 * Process SVG sprite generation
 */
async function buildSvgSprite() {
	// Check if icons directory exists
	if (!existsSync(iconsSrcDir)) {
		console.warn(`[SVG Sprite] Icons directory not found: ${iconsSrcDir}`);
		console.warn(`[SVG Sprite] Skipping sprite generation.`);
		return;
	}

	// Find all SVG files in icons directory
	const iconFiles = await fg('*.svg', {
		cwd: iconsSrcDir,
		absolute: true,
		caseSensitiveMatch: false,
	});

	if (iconFiles.length === 0) {
		console.warn(`[SVG Sprite] No SVG icons found in: ${iconsSrcDir}`);
		console.warn(`[SVG Sprite] Skipping sprite generation.`);
		return;
	}

	console.log(`[SVG Sprite] Found ${iconFiles.length} icon(s)`);

	// Create sprite store
	const sprite = svgstore({
		cleanDefs: true,
		cleanSymbols: true,
		inline: true,
	});

	// Process each icon
	for (const iconFile of iconFiles) {
		try {
			// Read SVG file
			const svgContent = readFileSync(iconFile, 'utf8');

			// Optimize SVG with SVGO
			const optimized = svgoOptimize(svgContent, {
				multipass: true,
				plugins: [
					{
						name: 'preset-default',
						params: { overrides: { removeViewBox: false } },
					},
				],
			});

			// Get icon name from filename (without extension)
			const iconName = path.basename(iconFile, '.svg');

			// Add to sprite store
			sprite.add(iconName, optimized.data);

			if (isDev) {
				console.log(`[SVG Sprite] Added icon: ${iconName}`);
			}
		} catch (error) {
			console.error(
				`[SVG Sprite] Error processing ${iconFile}:`,
				error.message
			);
		}
	}

	// Generate sprite SVG for external references
	// Set inline: false to support external href references
	let spriteSvg = sprite.toString({
		inline: false,
	});

	// Ensure sprite has proper xmlns attributes for external references
	// Add xmlns:xlink if not present for better browser compatibility
	if (!spriteSvg.includes('xmlns:xlink') && spriteSvg.includes('<svg')) {
		spriteSvg = spriteSvg.replace(
			/<svg([^>]*)>/,
			'<svg$1 xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">'
		);
	}

	// Ensure destination directory exists
	ensureDirectoryExistence(spriteDestDir);

	// Write sprite file
	writeFileSync(spriteDestFile, spriteSvg, 'utf8');

	console.log(`[SVG Sprite] Generated: ${spriteDestFile}`);
	console.log(`[SVG Sprite] Total icons: ${iconFiles.length}`);
}

// Run the build
buildSvgSprite().catch((error) => {
	console.error('[SVG Sprite] Build failed:', error);
	process.exit(1);
});
