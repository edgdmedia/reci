import fs from 'node:fs';
import path from 'node:path';

const root = process.cwd();
const imageExtensions = new Set(['.gif', '.jpeg', '.jpg', '.png', '.webp']);
const scanRoots = ['assets', 'wordpress/assets'];
const allowedNamePatterns = [
	/favicon/i,
	/logo/i,
	/reci[-_ ]?collab/i,
	/pitt[-_]?icon/i,
	/pitt[-_]?logo/i,
];

function walk(directory, files = []) {
	if (!fs.existsSync(directory)) {
		return files;
	}

	for (const entry of fs.readdirSync(directory, { withFileTypes: true })) {
		const fullPath = path.join(directory, entry.name);
		if (entry.isDirectory()) {
			walk(fullPath, files);
			continue;
		}

		files.push(fullPath);
	}

	return files;
}

const violations = scanRoots
	.flatMap((scanRoot) => walk(path.join(root, scanRoot)))
	.filter((file) => imageExtensions.has(path.extname(file).toLowerCase()))
	.filter((file) => !allowedNamePatterns.some((pattern) => pattern.test(path.basename(file))))
	.map((file) => path.relative(root, file));

if (violations.length > 0) {
	console.error('Theme image boundary violations:');
	for (const violation of violations) {
		console.error(`- ${violation}`);
	}
	process.exit(1);
}

console.log('Theme image boundary check passed.');
