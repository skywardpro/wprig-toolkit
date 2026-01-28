#!/usr/bin/env node
import { execSync } from 'child_process';

try {
	console.log('Running CSS linting...');
	execSync('npm run lint:css', { stdio: 'inherit' });
	
	console.log('Running Prettier...');
	execSync('npm run prettier:fix', { stdio: 'inherit' });
	
	console.log('Adding formatted files back to staging area...');
	execSync('git add -u', { stdio: 'inherit' });
	
	console.log('✅ Pre-commit checks passed!');
	process.exit(0);
} catch (error) {
	console.error('❌ Pre-commit checks failed. Commit aborted.');
	process.exit(1);
}