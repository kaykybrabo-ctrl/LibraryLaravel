export default {
  data() {
    return {
      currentUser: null,
      authToken: '',
      showDropdown: false,
      routePage: 'home',
      successMessage: '',
      errorMessage: '',

      authLoading: false,
      authFieldErrors: {},
      
      showRegister: false,
      showResetForm: false,
      resetEmail: '',
      resetCode: '',
      resetToken: '',
      forgotEmail: '',
      newPassword: '',
      newPasswordConfirm: '',
      resetNewPassword: '',
      resetNewPasswordConfirm: '',
      resetPasswordVisible: false,
      resetPasswordConfirmVisible: false,
      
      loginForm: {
        email: '',
        password: '',
      },
      loginPasswordVisible: false,
      
      registerForm: {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
      },
      registerPasswordVisible: false,
    };
  },
};
