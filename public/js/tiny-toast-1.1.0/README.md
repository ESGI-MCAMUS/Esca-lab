# tiny-toast

## What is Tiny Toast
Tiny Toast is a small library in pure Javascript and compiled SASS made to display informations inside a Toast.

![Toast displaying an information](/References/Images/info-toast.png)

![Toast displaying a success](/References/Images/success-toast.png)

![Toast displaying a warning](/References/Images/warning-toast.png)

![Toast displaying an error](/References/Images/error-toast.png)

## How to use it ?

### Implementation
Include in the header of the page both css and js files.

* _Adding the CSS file_
    ```html
    <link rel="stylesheet" type="text/css" href="PATH/dist/css/tiny-toast.min.css">
    ```

* _Adding the Javascript file_
    ```html
    <script src="PATH/dist/js/tiny-toast.min.js"></script>
    ```

In order to use it correctly with the icons you have to add a CDN of [Font Awesome](https://fontawesome.com/). Otherwise you won't be able to display icons on the Toast.

### Displaying a Toast
In order to display a Toast, you first have to build a Toast object and then displaying it.

#### Creating the Toast
To create a Toast simply use this syntax :
```js
let my_new_toast = new Toast('id', 'type', 'icon', 'text');
```
Where :
* `id` is a String that represent the id of the Toast on the DOM
* `type` is a String that can either be : `info`,`success`, `warning` or `error`. Each of those types is linked to a color so the user can understand at first glance what is being displayed
* `icon` is a String that correspond to the Font Awesome icon that will be displayed
* `text` is a String of the message that will be displayed

On top of that exists two other optional parameters that can be added :
* `dismissible` is a Boolean turned to `false` by default. It indicates if the Toast is removed automatically or by hand. If turned to `true` the user will have to click on it.
* `animated` is a Boolean turned to `false`
 by default. If turned to true at the end of the entrance, the icon will be animated.

 In order to use the `animated` options, the `dismissible` flag have to be written. If you don't want the Toast to be `dismissible` but `animated` just put `dismissible` at false.

#### Displaying the Toast
Once the object is created simply use the `.show()` function like this : 
```js
my_new_toast.show()
```

If you don't want to assing the object to a variable in a case of a one-time notification for example, you can create and display a Toast using this syntax :
```js
new Toast('id', 'type', 'icon', 'text').show();
```

#### Removing the Toast
The Toast will remove itself from the view and the DOM if the dismissible flag is turned to false. However if at true it will disapear, in the same way, only after being clicked on.

### Customizing the style
The CSS is generated from a SASS file located at `/src/style/sass/tiny-toast.sass`. If you want to have your own style simply modify the SASS file to regenerate a CSS file.