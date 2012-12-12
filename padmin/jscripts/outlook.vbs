sub contact_OnClick()

On Error resume next
olFolderContacts = 10


Set appOutlook = CreateObject("Outlook.Application")

If Err <> 0 or appOutlook is Nothing Then

  msgbox "ERROR : Internet Explorer has wrong security settings. Please modify security setting to low!"

  else

  Set mapiNameSpace = appOutlook.GetNameSpace("MAPI")
  
    'find contact
  Set objContacts = mapiNameSpace.GetDefaultFolder(olFolderContacts)
  'Msgbox  "[FullName] = " & chr(34) & document.form1.form1_name_account.value & chr(34) & ""
 
  Set objContact = objContacts.Items.Find("[Email1Address] = " & chr(34) & document.form1.form1_txt_email1.value & chr(34) & "")
  If Not TypeName(objContact) = "Nothing" Then
    response=MsgBox ("Contact already exists !. Do you want to overwrite it ?",vbYesNo,"Outlook")
    
    if response = vbYes then 
    objContact.FullName = document.form1.form1_name_account.value 
    objContact.HomeTelephoneNumber = document.form1.form1_txt_telf1.value
    objContact.HomeFaxNumber = document.form1.form1_txt_fax.value
    objContact.HomeAddressStreet = document.form1.form1_txt_address1.value
    objContact.HomeAddressCity = document.form1.form1_txt_poblacion.value
    'objContact.HomeAddressState = "Leicestershire"
    objContact.HomeAddressPostalCode = document.form1.form1_txt_cp.value
    'objContact.Nickname = "No Cats Kif"
    objContact.Email1Address = document.form1.form1_txt_email1.value
    objContact.WebPage = document.form1.form1_txt_web.value
    'objContact.ComputerNetworkName = "HouseNet"    
    objContact.Save

  Set objContact = Nothing 
  end if
    
    Else
  'MsgBox "Contact not found."
  Set objContact = appOutlook.CreateItem(2) // Crear un contacto en la carpeta contactos
  'objContact.FirstName = document.form1.form1_name_account.value
  'objContact.LastName = "AAAAAA"
  objContact.FullName = document.form1.form1_name_account.value 
  objContact.HomeTelephoneNumber = document.form1.form1_txt_telf1.value
  objContact.HomeFaxNumber = document.form1.form1_txt_fax.value
  objContact.HomeAddressStreet = document.form1.form1_txt_address1.value
  objContact.HomeAddressCity = document.form1.form1_txt_poblacion.value
  'objContact.HomeAddressState = "Leicestershire"
  objContact.HomeAddressPostalCode = document.form1.form1_txt_cp.value
  'objContact.Nickname = "No Cats Kif"
  objContact.Email1Address = document.form1.form1_txt_email1.value
  objContact.WebPage = document.form1.form1_txt_web.value
  'objContact.ComputerNetworkName = "HouseNet"

  objContact.Save

  Set objContact = Nothing
  End If

end if 





End sub