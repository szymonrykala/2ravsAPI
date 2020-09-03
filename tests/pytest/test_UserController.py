from API.UserController import User
import pytest

pytest.verified = False

# @pytest.fixture(scope='module')
# def user():
#     return User()

def setup_module(module):
    global user
    user = User()

@pytest.mark.parametrize('email, password, httpCode',
                         [
                             (
                                 'weronika1212@gmail.com',
                                 'weronika1214',
                                 200
                             ), (
                                 'weronika1212@gmail.com',
                                 'nieprawdziwe hasło',
                                 401
                             )
                         ])
def test_verifyUser(email, password, httpCode):
    resp = user.verifyUser(email, password)
    json = resp.json()
    assert resp.status_code == httpCode
    if json['succes']: 
        user.auth = json['data']['jwt']


def test_registerUser():
    pass


def test_activateUser():
    pass


def test_resendActivationEmail():
    pass


@ pytest.mark.skipif(pytest.verified == False, reason="user not verified")
def test_logoutUser():
    pass


@ pytest.mark.skipif(pytest.verified == False, reason="user not verified")
def test_getAllUsers():
    pass


@ pytest.mark.skipif(pytest.verified == False, reason="user not verified")
def test_getSpecificUser():
    pass


@ pytest.mark.skipif(pytest.verified == False, reason="user not verified")
def test_updateUserInformations(id, data):
    pass


@ pytest.mark.skipif(pytest.verified == False, reason="user not verified")
def test_deleteUser(id):
    pass
