      var currentSkin = getCurrentSkin();
      var stories = window.Zuck(document.querySelector('#stories'), {
        backNative: true,
        previousTap: true,
        skin: currentSkin['name'],
        autoFullScreen: currentSkin['params']['autoFullScreen'],
        avatars: currentSkin['params']['avatars'],
        paginationArrows: currentSkin['params']['paginationArrows'],
        list: currentSkin['params']['list'],
        cubeEffect: currentSkin['params']['cubeEffect'],
        localStorage: true,
			stories: [
			  {
				id: 'ramon',
				photo:
				  './images/stories/stories1.jpg',
				name: 'Ramon',
				time: timestamp(),
				items: [
              {
                id: 'ramon-1',
                type: 'photo',
                length: 3,
                src: './images/stories/storyfull-1.jpg',
                preview:
                  './images/stories/storyfull-1.jpg',
                link: '',
                linkText: false,
                time: timestamp()
              },
              {
                id: 'ramon-2',
                type: 'video',
                length: 0,
                src: './images/stories/story-video.mp4',
                preview:
                './images/stories/story-video.mp4',
                link: '',
                linkText: false,
                time: timestamp()
              },
              {
                id: 'ramon-3',
                type: 'photo',
                length: 3,
                src: './images/stories/storyfull-8.jpg',
                preview:
                './images/stories/storyfull-8.jpg',
                link: 'https://themeforest.net/user/dewwater/portfolio',
                linkText: 'Visit my Portfolio',
                time: timestamp()
              }
            ]
          },
          {
            id: 'gorillaz',
            photo:
              './images/stories/stories2.jpg',
            name: 'Gorillaz',
            time: timestamp(),
            items: [
              {
                id: 'gorillaz-1',
                type: 'video',
                length: 0,
                src: './images/stories/story-video2.mp4',
                preview:
                './images/stories/storyfull-3.jpg',
                link: '',
                linkText: false,
                time: timestamp()
              },
              {
                id: 'gorillaz-2',
                type: 'photo',
                length: 3,
                src: './images/stories/storyfull-4.jpg',
                preview:
                './images/stories/storyfull-4.jpg',
                link: '',
                linkText: false,
                time: timestamp()
              }
            ]
          },
          {
            id: 'ladygaga',
            photo:
              './images/stories/stories3.jpg',
            name: 'Lady Gaga',
            time: timestamp(),
            items: [
              {
                id: 'ladygaga-1',
                type: 'photo',
                length: 5,
                src: './images/stories/storyfull-2.jpg',
                preview:
                './images/stories/storyfull-2.jpg',
                link: '',
                linkText: false,
                time: timestamp()
              },
              {
                id: 'ladygaga-2',
                type: 'photo',
                length: 3,
                src: './images/stories/storyfull-6.jpg',
                preview:
                './images/stories/storyfull-6.jpg',
                link: 'http://ladygaga.com',
                linkText: false,
                time: timestamp()
              }
            ]
          },
          {
            id: 'starboy',
            photo:
              './images/stories/stories4.jpg',
            name: 'The Weeknd',
            time: timestamp(),
            items: [
              {
                id: 'starboy-1',
                type: 'photo',
                length: 5,
                src: './images/stories/storyfull-7.jpg',
                preview:
                './images/stories/storyfull-7.jpg',
                link: '',
                linkText: false,
                time: timestamp()
              }
            ]
          },
          {
            id: 'riversquomo',
            photo:
              './images/stories/stories5.jpg',
            name: 'Rivers Cuomo',
            time: timestamp(),
            items: [
              {
                id: 'riverscuomo-1',
                type: 'photo',
                length: 10,
                src: './images/stories/storyfull-1.jpg',
                preview:
                  './images/stories/storyfull-1.jpg',
                link: '',
                linkText: false,
                time: timestamp()
              }
            ]
          }
        ]
      });