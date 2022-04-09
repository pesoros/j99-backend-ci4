<?= $this->include('mail/headermail') ?>

<table border="0" align="center" cellpadding="0" cellspacing="0" class="row" role="presentation" width="640" style="width:640px;max-width:640px;" Simpli>
  <!-- simpli-header-7 -->
  <tr>
    <td align="center">

<table border="0" align="center" cellpadding="0" cellspacing="0" class="row container-padding10" role="presentation" width="640" style="width:640px;max-width:640px;">
  

<!--[if (gte mso 9)|(IE)]><v:rect xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false" style="width:640px;">
<v:fill type="frame" src="images/header-7.jpg" color="#F4F4F4" />
<v:textbox style="mso-fit-shape-to-text:true;" inset="0,0,0,0"><![endif]-->

<div>
<!-- simpli-header-bg-image -->

<!-- Content -->
<table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" class="row" width="600" style="width:600px;max-width:600px;">
  <tr>
    <td height="0" valign="bottom" class="container-padding" style="font-size:640px;line-height:640px;" Simpli>

    <table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="width:100%;max-width:100%;">
      <tr>
        <td align="center" Simpli bgcolor="#FFFFFF" height="40"  style="height:40px;font-size:40px;line-height:36px;border-radius:36px 36px 0 0;" class="container-padding">&nbsp;</td>
      </tr>
    </table>

    </td>
  </tr>
</table>
<!-- Content -->

    </td>
  </tr>
</table>
<!-- simpli-header-bg-image -->
</div>

<!--[if (gte mso 9)|(IE)]></v:textbox></v:rect><![endif]-->

    </td>
  </tr>
  <!-- bg-image -->
</table>

<table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" class="row container-padding25" width="600" style="width:600px;max-width:600px;">
  <!-- basic-info -->
  <tr>
    <td align="center" Simpli bgcolor="#FFFFFF"  style="border-radius:0 0 36px 36px; border-bottom:solid 6px #DDDDDD;">
      <!-- content -->
      <table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" class="row container-padding" width="520" style="width:520px;max-width:520px;">
        <tr>
          <td class="center-text" Simpli align="center" style="font-family:'Catamaran',Arial,Helvetica,sans-serif;font-size:28px;line-height:36px;font-weight:500;font-style:normal;color:#1898c2;text-decoration:none;letter-spacing:0px;">
              <singleline>
                <div mc:edit Simpli>
                  OTP
                </div>
              </singleline>
          </td>
        </tr>
        <tr>
          <td class="center-text" Simpli align="center" style="font-family:'Catamaran',Arial,Helvetica,sans-serif;font-size:48px;line-height:54px;font-weight:700;font-style:normal;color:#333333;text-decoration:none;letter-spacing:0px;">
              <singleline>
                <div mc:edit Simpli>
                  <?= $otpNumber ?>
                </div>
              </singleline>
          </td>
        </tr>
        <tr>
          <td height="75" style="font-size:75px;line-height:75px;" Simpli>&nbsp;</td>
        </tr>
      </table>
      <!-- content -->
    </td>
  </tr>
  <!-- basic-info -->
</table>

    </td>
  </tr>
  <!-- simpli-header-7 -->
</table>

<table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="width:100%;max-width:100%;" Simpli>
  <!-- simpli-footer -->
  <tr>
    <td align="center">
      
    <?= $this->include('mail/footermail') ?>

    </td>
  </tr>
  <!-- simpli-footer -->
</table>

    </td>
  </tr><!-- Outer-Table -->
</table>

</body>
</html>
