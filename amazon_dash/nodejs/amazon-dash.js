var dash_button = require('node-dash-button'),
    dash = dash_button(["--:--:--:--:--:--","--:--:--:--:--:--"], null, null, 'all'), 
    exec = require('child_process').exec;

dash.on('detected', function(dash_id) {
    if (dash_id === "--:--:--:--:--:--") {
      exec('sh ./dash_unko.sh', function(error, stdout, stderr) {
          console.log('stdout: ' + stdout);
          console.log('stderr: ' + stderr);
          if (error !== null) {
              console.log('exec error: ' + error);
          }
      });
    } else if (dash_id === "--:--:--:--:--:--") {
      exec('sh ./dash_seiri.sh', function(error, stdout, stderr) {
          console.log('stdout: ' + stdout);
          console.log('stderr: ' + stderr);
          if (error !== null) {
              console.log('exec error: ' + error);
          }
      });
    }
});
