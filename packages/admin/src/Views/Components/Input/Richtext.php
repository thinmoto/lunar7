<?php

namespace Lunar\Hub\Views\Components\Input;

use Illuminate\View\Component;

class Richtext extends Component
{
	/**
	 * The initial value.
	 *
	 * @var string
	 */
	public $initialValue = null;
	public $upload = '';

	/**
	 * The set of options for the rich text field.
	 */
	public array $options = [
		'theme' => 'snow',
	];

	/**
	 * Instantiate the component.
	 *
	 * @param  string  $initialValue
	 */
	public function __construct($initialValue = null, array $options = [], string $upload = '')
	{
		$this->initialValue = $initialValue;
		$this->options = array_merge($this->options, $options);
		$this->upload = $upload ?: route('lunar.hub.quill-upload');

		if($upload)
		{
			// $this->options['modules'] = [
			// 	'imageUploader' => [
			// 		'upload' => '
			// 			(file) => {
			//                 return new Promise((resolve, reject) => { }
			//             }
			//         ',
			// 	]
			// ];

			// $this->options['modules'] = [
			// 	'imageUpload' => ' {
			// 			url: "'.$upload.'", // server url. If the url is empty then the base64 returns
			//             name: "image", // custom form name
			//             withCredentials: false, // withCredentials
			//             headers: {}, // add custom headers, example { token: "your-token"}
			//             csrf: { token: "'.csrf_token().'", hash: "" }, // add custom CSRF
			//             customUploader: () => {}, // add custom uploader
			//             // personalize successful callback and call next function to insert new url to the editor
			//             callbackOK: (serverResponse, next) => {
			//                 next(serverResponse);
			//             },
			//             // personalize failed callback
			//             callbackKO: serverError => {
			//                 alert(serverError);
			//             },
			//             // optional
			//             // add callback when a image have been chosen
			//             checkBeforeSend: (file, next) => {
			//                 console.log(file);
			//                 next(file); // go back to component and send to the server
			//             }
			//         }
			// 	'
			// ];
		}
	}

	/**
	 * Get a unique instance id.
	 *
	 * @return string
	 */
	protected function getInstanceId()
	{
		$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < 25; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}

		return $randomString;
	}

	/**
	 * Get the view / contents that represent the component.
	 *
	 * @return \Illuminate\View\View|\Closure|string
	 */
	public function render()
	{
		return view('adminhub::components.input.richtext', [
			'instanceId' => $this->getInstanceId(),
		]);
	}
}
