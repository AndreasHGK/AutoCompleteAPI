# AutoCompleteAPI
Set up command autocompletion for your commands

With **AutoCompleteAPI** you can make it easier for users to execute your commands.
Ever wondered how you can get autocompletion and paramaters listed when you type a command?
With this API you can do it.

## API
This is a quick documentation on how you can add autocompletion and parameters to a command.
The command does not need to be from your plugin. You can do all of the following things in onEnable()

### Normal Parameters
This will be an example for /teleport

First of all, you want to get the main plugin class. You could do `AutoCompleteAPI::getInstance()`,
but this would make your plugin always require the user to have AutoCompleteAPI installed.
It is better to start with this:
```PHP
$AutoCompleteAPI = $this->getServer()->getPluginManager()->getPlugin("AutoCompleteAPI");
```
If you do this you should also add AutoCompleteAPI as softdepend in your plugin.yml .

Next, check if the plugin exists on the server.
```PHP
$AutoCompleteAPI = $this->getServer()->getPluginManager()->getPlugin("AutoCompleteAPI");

if(isset($AutoCompleteAPI)){
    //this is where you should register the parameters.
}
```

Now you can register your commands to the plugin. To do this you need a command class.
You can put this code at the same part of where you're registering commands to the server,
or alternatively you can get the command from the commandmap.
```PHP
$AutoCompleteAPI = $this->getServer()->getPluginManager()->getPlugin("AutoCompleteAPI");

if(isset($AutoCompleteAPI)){
    $command = $this->getServer()->getCommandMap()->getCommand("teleport"); // = pocketmine's Command class
    $customCommandData = $AutoCompleteAPI->registerCommandData($command); //this returns the CustomComandData class while registering.
}
```

The following step is to actually add the parameters. To add a normal paramater, use `CustomCommandData::normalParamater()`.
```PHP
$AutoCompleteAPI = $this->getServer()->getPluginManager()->getPlugin("AutoCompleteAPI");

if(isset($AutoCompleteAPI)){
    $command = $this->getServer()->getCommandMap()->getCommand("teleport");
    $customCommandData = $AutoCompleteAPI->registerCommandData($command);
    
    $customCommandData->normalParameter(0, 0, CustomCommandData::ARG_TYPE_TARGET, "Player", false);
}
```
Let's quickly explain what all those arguments are. I will come back on the first 0 later.
The second 0, the Y-value, is the location of the paramater. For example, if it's 0 it will be the first parameter,
when it's 1 the second one and so on. The third value is the type of the parameter.
Look in the CustomCommandData class to see what the types are. "player" is the name of the parameter.
Lastly, the false is the optional value. This will just change the brackets to square brackets.

Now, in vanilla minecraft when you do /teleport it will show you multiple lines of different parameters. This is what the first 0, the X-value, is for.
0 will be the first one, 1 the second one and so on. Let's add multiple lines to /teleport
```PHP
$AutoCompleteAPI = $this->getServer()->getPluginManager()->getPlugin("AutoCompleteAPI");

if(isset($AutoCompleteAPI)){
    $command = $this->getServer()->getCommandMap()->getCommand("teleport");
    $customCommandData = $AutoCompleteAPI->registerCommandData($command);
    
    $customCommandData->normalParameter(0, 0, CustomCommandData::ARG_TYPE_TARGET, "Player", false);
    
    $customCommandData->normalParameter(1, 0, CustomCommandData::ARG_TYPE_TARGET, "From", false);
    $customCommandData->normalParameter(1, 1, CustomCommandData::ARG_TYPE_TARGET, "To", false);
    
    $customCommandData->normalParameter(2, 0, CustomCommandData::ARG_TYPE_POSITION, "Position", false);
    
    $customCommandData->normalParameter(3, 0, CustomCommandData::ARG_TYPE_TARGET, "Player", false);
    $customCommandData->normalParameter(3, 1, CustomCommandData::ARG_TYPE_POSITION, "Position", false);
}
```
Now you have set up simple parameters for your command!

### Magic parameters
There are to types of magic parameters. **MAGIC_TYPE_ITEM** and **MAGIC_TYPE_BLOCK**.
If you use these, all types of block/items will show up with icons in a list. Adding them is simple.

In the code you had before, you can do this to add a magic parameter.
```PHP
$customCommandData->magicParameter(0, 0, CustomCommandData::MAGIC_TYPE_ITEM, "ItemType", false);
```
The first 0 is the X-value, and the second one the Y-value. They do the same thing as before.
For the type however, you need to use one of the MAGIC_TYPES in the constants in the CustomCommandData class.
the fourth value is the name of the parameter and the fifth value is the optional value.

### Array parameters
Lastly I'll show how you can add array parameters.

In the same place, add this:
```PHP
$customCommandData->arrayParameter(0, 0, "Array", ["Value1", "Value2"], false, "ArrayType");
```
The first 2 values still do the same. The third value is the name of the parameter.
The fourth value is the contents of the array. Here you should list every value that needs to be in the array.
The fith value is the optional value and the last one is the TypeName. This doesn't always show up,
but it is the name of the type of all the values in the array.

## Credits
This plugin is developed by **AndreasHGK**.
If you have contributed to the plugin and you feel like you should be listed as a contributor, open an issue on github. 