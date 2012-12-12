dim dialog,response, overnot

Function scontacts(url)

Set xmlDoc = CreateObject("Msxml2.DOMDocument.3.0")


xmlDoc.async = False
xmlDoc.Load(url)
If (xmlDoc.parseError.errorCode <> 0) Then
   Dim myErr
   Set myErr = xmlDoc.parseError
   MsgBox("You have error " & myErr.reason)
Else
   overnot=MsgBox (" Do you want to overwrite all ?",vbYesNo+vbQuestion,"Outlook")
   'Self.ResizeTo 1,1
   ShowProgress
   'xmlDoc.setProperty "SelectionLanguage", "XPath"
   set sel = xmlDoc.documentElement.selectNodes("/data/row")
   'msgbox sel.length
   for i=0 To sel.length-1
      UpdateProgress i, sel.length-1
      Dialog.Progress.innerHtml = "pass " & i   
      'contact = sel.item(i).selectsinglenode("FullName").text
      update_contact(sel.item(i))
      dialog.Focus()
      if response = vbCancel then 
         exit for 
      end if
      'MsgBox contact
      
   next
   HideProgress
   self.Focus()
   'self.ResizeTo 1024,768
   'MsgBox xmlDoc.xml
End If
scontacts=false
End function

'********************************************************************************************************
'Creates Outlook contacts
'********************************************************************************************************
sub update_contact(contact)

On Error resume next
olFolderContacts = 10


Set appOutlook = CreateObject("Outlook.Application")

If Err <> 0 or appOutlook is Nothing Then

  msgbox "ERROR : Internet Explorer has wrong security settings. Please modify security setting to low!"

  else

  Set mapiNameSpace = appOutlook.GetNameSpace("MAPI")
  
    'find contact
  Set objContacts = mapiNameSpace.GetDefaultFolder(olFolderContacts)
  'Msgbox contact.selectsinglenode("txt_email1").text
 
  Set objContact = objContacts.Items.Find("[Email1Address] = " & chr(34) & contact.selectsinglenode("txt_email1").text & chr(34) & "")
  If Not TypeName(objContact) = "Nothing" Then
    
    if overnot=vbNo then 
        response=MsgBox ("Contact " & contact.selectsinglenode("name_account").text & " already exists !. Do you want to overwrite it ?",vbYesNoCancel+vbQuestion,"Outlook")
        else 
        response=vbYes 
        end if
    
    if response = vbCancel then 
       Set objContact = Nothing 
       exit sub 
       end if
    
    if response = vbYes then 
    objContact.FullName = contact.selectsinglenode("name_account").text
    objContact.HomeTelephoneNumber = contact.selectsinglenode("txt_phone").text
    objContact.HomeFaxNumber = contact.selectsinglenode("txt_fax").text
    objContact.HomeAddressStreet = contact.selectsinglenode("txt_address1").text
    objContact.HomeAddressCity = contact.selectsinglenode("txt_poblacion").text
    'objContact.HomeAddressState = "Leicestershire"
    objContact.HomeAddressPostalCode = contact.selectsinglenode("txt_cp").text
    'objContact.Nickname = "No Cats Kif"
    objContact.Email1Address = contact.selectsinglenode("txt_email1").text
    objContact.WebPage = contact.selectsinglenode("txt_web").text
    'objContact.ComputerNetworkName = "HouseNet"    
    objContact.Save
    Set objContact = Nothing 
    end if
    
    Else
  'MsgBox "Contact not found."
  Set objContact = appOutlook.CreateItem(2) ' Crear un contacto en la carpeta contactos
  
   objContact.FullName = contact.selectsinglenode("name_account").text
    objContact.HomeTelephoneNumber = contact.selectsinglenode("txt_phone").text
    objContact.HomeFaxNumber = contact.selectsinglenode("txt_fax").text
    objContact.HomeAddressStreet = contact.selectsinglenode("txt_address1").text
    objContact.HomeAddressCity = contact.selectsinglenode("txt_poblacion").text
    'objContact.HomeAddressState = "Leicestershire"
    objContact.HomeAddressPostalCode = contact.selectsinglenode("txt_cp").text
    'objContact.Nickname = "No Cats Kif"
    objContact.Email1Address = contact.selectsinglenode("txt_email1").text
    objContact.WebPage = contact.selectsinglenode("txt_web").text
    'objContact.ComputerNetworkName = "HouseNet"    

  objContact.Save

  Set objContact = Nothing
  End If

end if 
end sub
'**********************************************************************************


sub ShowProgress

Set dialog = window.Open("about:blank","ProgressWindow","height=15,width=375,left=300,top=300,status=no,titlebar=no,toolbar=no,menubar=no,location=no,scrollbars=no")

dialog.Focus()
dialog.ResizeTo 375,15
dialog.MoveTo 100,200
dialog.document.body.style.fontFamily = "Helvetica"
dialog.document.body.style.fontSize = "10pt"
dialog.document.writeln "<html><body>" & "<OBJECT ID=""ProgressBar"" WIDTH=350 HEIGHT=22 CLASSID=""CLSID:8BD21D10-EC42-11CE-9E0D-00AA006002F3"">" _
& "   <PARAM NAME=""VariousPropertyBits"" VALUE=""746604571"">" _
& "   <PARAM NAME=""ForeColor"" VALUE=""2147483650"">" _
& "   <PARAM NAME=""BackColor"" VALUE=""2147483663"">" _
& "   <PARAM NAME=""Value"" VALUE="""">" _
& "   <PARAM NAME=""FontName"" VALUE=""Wingdings"">" _
& "   <PARAM NAME=""FontHeight"" VALUE=""200"">" _
& "   <PARAM NAME=""FontCharSet"" VALUE=""2"">" _
& "</OBJECT>" _
& "<div id=""Progress""></div></body></html>"

dialog.document.title = "Please wait."
dialog.document.body.style.backgroundColor = "buttonface"
dialog.document.body.style.borderStyle = "none"
dialog.document.body.style.marginTop = 15

end sub
'********************************************************************************
sub UpdateProgress ( numdone , total )

if total <= 0 then
numblks = 100 \ 3
else
numblks = ((100.0 * numdone) / total) \ 3
end if

dialog.document.ProgressBar.Value = String(numblks,"n")

end sub
'**********************************************************************************
sub HideProgress

dialog.Close

end sub
'*******************************************************************************
Sub progress_example

alert "when you click OK, the main window will shrink and the progress window will be displayed"

Self.ResizeTo 1,1

ShowProgress

alert "when you click OK, the progress meter will start running"

for i = 1 to 100
UpdateProgress i,100
Dialog.Progress.innerHtml = "pass " & i
for j = 1 to 10000
next
next

alert "when you click OK, the progress window will close and the main window will redisplay"

HideProgress

self.Focus()
self.ResizeTo 200,100

End Sub


