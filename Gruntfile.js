module.exports = function(grunt) {
  
  grunt.registerTask('watch', [ 'watch' ]);
  
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    // concat
    concat: {
         js: {
           options: {
             separator: ';'
           },
           src: ['assets/js/src/**/*.js'],
           dest: 'assets/js/<%= pkg.name %>.min.js'
         },
       },

    // uglify
    uglify: {
        options: {
          mangle: false
        },
        js: {
          files: {
            'assets/js/<%= pkg.name %>.min.js': ['assets/js/<%= pkg.name %>.min.js']
          }
        }
      },

    // watch our project for changes
    watch: {
      // JS
      js: {
        files: ['assets/js/src/**/*.js'],
        tasks: ['concat:js', 'uglify:js'],
          options: {
        //    livereload: true,
          }
      },
    
    }
  });

  // Saves having to declare each dependency
  require( "matchdep" ).filterDev( "grunt-*" ).forEach( grunt.loadNpmTasks );

  grunt.registerTask('default', ['concat', 'uglify' ]);
};