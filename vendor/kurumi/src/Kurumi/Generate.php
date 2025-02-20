<?php

namespace Kurumi\Kurumi;

/** ---------------
 *  new Generate
 *  sebuah class yang di gunakan untuk mengenerate ulang sebuah
 *  file kurumi mentah resources/view menjadi file kurumi matang storage/framework/views
 */

class Generate extends Regex
{
  public function __construct()
  {
    $this->generate();
  }

  private function generate()
  {
    $files = array_merge(glob('./resources/views/*php'), glob('./resources/views/**/*php'), glob('./resources/views/**/**/*php'), glob('./resources/views/**/**/**/*php'), glob('./resources/views/**/**/**/**/*php'), glob('./resources/views/**/*php'));

    foreach ($files as $file) {
      $filename = str_replace('./resources/views/', "", $file); // yang udah di pisah
      $filename = str_replace("/", ".", $filename);

      $contents = file_get_contents($file);
      $file_new = fopen("./storage/framework/views/" . $filename, 'w');
      fwrite($file_new, $this->run($contents));
      fclose($file_new);
    }
  }
}
