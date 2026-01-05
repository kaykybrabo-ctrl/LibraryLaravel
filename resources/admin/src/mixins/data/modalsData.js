export default {
  data() {
    return {
      showPixModal: false,
      pixCode: '',
      pixAmount: 0,
      
      showRentModal: false,
      rentTargetBook: null,
      rentReturnDate: '',
      
      showCreateBookModal: false,
      showEditBookModal: false,
      editBook: null,
      newBookAuthorMode: 'existing',
      newBookError: '',
      newBook: {
        title: '',
        author_id: '',
        description: '',
        photo: '',
        price: null,
      },
      
      newAuthor: {
        name: '',
        bio: '',
        photo: '',
      },
      showCreateAuthorModal: false,
      showEditAuthorModal: false,
      editAuthor: null,
      
      showConfirmModal: false,
      confirmTitle: '',
      confirmMessage: '',
      confirmConfirmLabel: '',
      confirmCancelLabel: '',
      confirmIsDanger: false,
      confirmCallback: null,
      
      showUploadModal: false,
      uploadTarget: null,
    };
  },
};
