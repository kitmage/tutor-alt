<?php
if ($argc !== 2 || !is_file($argv[1])) { fwrite(STDERR,"Usage: php scripts/verify-package.php tutor.zip\n"); exit(2); }
$zip=new ZipArchive();if(true!==$zip->open($argv[1])){fwrite(STDERR,"Cannot open ZIP\n");exit(1);} $roots=[];
for($i=0;$i<$zip->numFiles;$i++){ $name=$zip->getNameIndex($i);$roots[explode('/',$name)[0]]=true;if(str_starts_with($name,'tutor-alt/')){fwrite(STDERR,"Invalid tutor-alt root\n");exit(1);} }
if(array_keys($roots)!==['tutor']||false===$zip->locateName('tutor/tutor.php')||false===$zip->locateName('tutor/vendor/autoload.php')){fwrite(STDERR,"Package must contain only tutor/, tutor/tutor.php, and the production Composer loader\n");exit(1);}echo "PASS: tutor/tutor.php package identity and production autoloader verified\n";
