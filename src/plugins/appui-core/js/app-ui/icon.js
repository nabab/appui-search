(() => {
  return {
    data() {
      return {
        root: appui.plugins['appui-search'] + '/',
        aiPlugin: appui.plugins['appui-ai'] ? appui.plugins['appui-ai'] + '/' : false,
        searchOn: false,
        isRegistered: false,
        isMicrophone: false,
        isLoading: false,
        chronoName: bbn.fn.randomString(),
        timeout: 500,
        mediaRecorder: null, // instance mediarecorder pour record l'audio
        audioChunks: [],     // chunks de l'audio
        audioBlob: null      // le blob de l'audio
      };
    },
    methods: {
      show() {
        this.searchOn = true;
      },
      onMouseDown() {
        if (this.aiPlugin) {
          bbn.fn.startChrono(this.chronoName);
          this.timeoutFn = setTimeout(() => this.isMicrophone = true, this.timeout)
        }
      },
      onMouseUp() {
        if (!this.aiPlugin) {
          this.show();
        }

        clearTimeout(this.timeoutFn);
        const duration = bbn.fn.stopChrono(this.chronoName);
        bbn.fn.log("DURATION: " + duration)
        if (duration < this.timeout) {
          this.show();
        }
        else {
          this.isLoading = true;
          this.isMicrophone = false;
        }
      },
      uploadAudio(audioBlob) {
        //const formData = new FormData();
        //formData.append('audio', audioBlob, 'recording.wav');
        const file = new File([audioBlob], 'recording.wav', {type: 'audio/wav'});
        const data = {
             audio: file
        };

        //for (let pair of formData.entries()) {
        //     console.log(pair[0], pair[1]);
        //}
        bbn.fn.log("enregistrement send")
        bbn.fn.log(this.aiPlugin + 'actions/voice-search')
        bbn.fn.upload(this.aiPlugin + 'actions/voice-search', data, d => {
        console.log("resultats de requete : ", d.data.success);
          if (d.data.success) {
            bbn.fn.log("Audio uploaded successfully");
            if (d.data.link) {
              bbn.fn.link(d.data.link);
            }
          } else {
            bbn.fn.log("Audio upload failed");
          }
          this.isLoading = false;
        });
      }
    },
    watch: {
      async isMicrophone(v) {
        if (v) {
          if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            try {
              // demande autorisation use le micro + get stream audio
              const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
              this.mediaRecorder = new MediaRecorder(stream); // creer l'instance de mediarecorder avec le stream
              // collecte les chunks audio
              this.mediaRecorder.ondataavailable = (event) => {
                if (event.data.size > 0) {
                  this.audioChunks.push(event.data); // add chunk au tab
                }
              };
              
              // quand record est stoppe
              this.mediaRecorder.onstop = () => {
                this.audioBlob = new Blob(this.audioChunks, { type: 'audio/wav' }); // on creer le blob audio
                this.audioChunks = []; //on vide le tab de chunk
  
                const audioUrl = URL.createObjectURL(this.audioBlob); // oncreer l'url
                this.$refs.audioPlayer.src = audioUrl;
                const audio = new Audio(audioUrl);
                audio.play();
  
                // on envoie le fichier au serveur
                this.uploadAudio(this.audioBlob);
              };
  
              this.mediaRecorder.start();
              bbn.fn.log("enregistrement son")
            } catch (err) {
              console.error("Error accessing  streamaudio: ", err);
            }
          } else {
            console.warn("getUserMedia not supported on your browser!");
          }
        } else {
          if (this.mediaRecorder) {
            this.mediaRecorder.stop();
            bbn.fn.log("enregistrement son stopped")
          }
        }
      },
      searchOn(val) {
        const search = appui.getRegistered('appui-search-big-search');
        if (val && search) {
          if (!this.isRegistered) {
            search.$on('close', () => {
              bbn.fn.log("BIG SEARVH CLOSING");
              this.searchOn = false;
            }, false, this);
            this.isRegistered = true;
          }

          search.show();
        }
      }
    }
  }
})();