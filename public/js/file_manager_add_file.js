
//***************************funkcija za upload vise fajlova****************************
var sledeci = 0; //cuva redni broj/ime sledeceg inputa fajla
function display() {
	var id = sledeci;
	document.getElementById('number_of_files').value = sledeci + 1;//upisujem koliko ima fajlova da bih znao koliko puta treba da vrtim petlju za upis u bazu
	document.getElementById('newInput'+id).innerHTML+=createInput(id);// ovo upisuje novi input na html stranicu
	var newInput = "fajl" + id;
	$('#file'+id).click();// automatski trazi da izaberes fajl kada pritisnes link za novi fajl
}
function createInput(id) {
	sledeci = id + 1;
	return "<p>Fajl "+ id +": <input type='file' id='file"+ id +"' name='"+id+"'></p><br />Komentar:<input type='text' name='"+id+"label'><br /><p id='newInput"+sledeci+"'>&nbsp;</p>";
}
