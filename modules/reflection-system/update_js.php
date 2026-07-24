<?php
$file = '/Users/olalekan/Projects/reci/media-hub/wordpress/modules/reflection-system/assets/js/builder.js';
$content = file_get_contents($file);

$target = 'a.jsxs("div",{children:[a.jsx("label",{className:"block text-xs font-medium text-gray-700 mb-1",children:"Text Color"}),a.jsx("input",{type:"color",className:"w-full h-8 cursor-pointer",value:e.color_text||"#e0e0e0",onChange:i=>t({color_text:i.target.value})})]})';

$replacement = 'a.jsxs("div",{children:[a.jsx("label",{className:"block text-xs font-medium text-gray-700 mb-1",children:"Heading Text Color"}),a.jsx("input",{type:"color",className:"w-full h-8 cursor-pointer",value:e.color_heading||"#e0e0e0",onChange:i=>t({color_heading:i.target.value})})]}),a.jsxs("div",{children:[a.jsx("label",{className:"block text-xs font-medium text-gray-700 mb-1",children:"Body Text Color"}),a.jsx("input",{type:"color",className:"w-full h-8 cursor-pointer",value:e.color_body||"#a0a0a0",onChange:i=>t({color_body:i.target.value})})]})';

$content = str_replace($target, $replacement, $content);

$target2 = 'color_text:l.colors.text';
$replacement2 = 'color_heading:l.colors.heading,color_body:l.colors.body';
$content = str_replace($target2, $replacement2, $content);

file_put_contents($file, $content);
echo "Updated js.\n";
