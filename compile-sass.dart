import 'dart:io';
import 'package:sass/sass.dart' as sass;

void main(List<String> arguments) {
  if (arguments.length != 2) {
    print('Usage: dart compile-sass.dart <input.scss> <output.css>');
    exit(1);
  }

  try {
    // New simplified compilation API
    var compiledCss = sass.compile(arguments[0]);
    File(arguments[1]).writeAsStringSync(compiledCss);
    print('Successfully compiled ${arguments[0]} to ${arguments[1]}');
  } catch (e) {
    stderr.writeln('Error compiling Sass: $e');
    exit(1);
  }
}
