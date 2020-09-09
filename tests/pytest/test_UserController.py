# from API.UserController import User
import pytest
import requests

storage = ''  # storage variable


class Storage():
    def __init__(self):
        self.url = "http://localhost:8080"
        self.__auth = ''

    @property
    def auth(self):
        return {"authorization": f"Bearer {self.__auth}"}

    @auth.setter
    def auth(self, token):
        self.__auth = token

# @pytest.fixture(scope='module')
# def user():
#     return User()


def setup_module(module):
    global storage
    storage = Storage()


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
    data = {'email': email, 'password': password}
    resp = requests.post(url=f'{storage.url}/auth', json=data)
    json = resp.json()
    assert resp.status_code == httpCode
    if json['succes']:
        storage.auth = json['data']['jwt']
        storage.userID = json['data']['userID']
        print(storage.auth)
        assert type(json['data']['jwt']) is str


@pytest.mark.parametrize('email, name, surname, password', [
    ('', 'imięTest', 'nazwizkoTest', 'testtest1214'),
    ('test@gmail.com', 'imięTest', 'nazwizkoTest', 'pasExcludeNum'),
    ('test@gmail.com', 'imięTest', '', 'testtest1214'),
    ('test@gmail.com', 'imięTest', 'nazwizkoTest', 'testtest1214'),
])
def test_registerUser(email, name, surname, password):
    data = {
        "email": email,
        "name": name,
        "surname": surname,
        "password": password
    }
    resp = requests.post(url=storage.url+'/users', json=data)
    json = resp.json()
    if resp.status_code == 201:
        # registered
        assert json['succes'] == True
    else:
        #not registered
        assert json['succes'] == False
        assert json['error']
        assert json['error']['code']
        assert json['error']['message']
        print(json['error']['message'])


# def test_activateUser():
#     pass


# def test_resendActivationEmail():
#     pass


# def test_logoutUser():
#     pass


@pytest.mark.parametrize('extensions, q', [
    ('acces', '?ext=acces_id'),
    ('', '')
])
def test_getAllUsers(extensions, q):
    resp = requests.get(
        url=storage.url+f'/users{q}',
        headers=storage.auth
    )
    json = resp.json()
    print(json)
    for user in json['data']:
        assert type(user['id']) == int
        assert type(user['name']) == str
        assert type(user['surname']) == str
        assert type(user['last_login']) == str
        assert type(user['email']) == str
        assert type(user['updated_at']) == str
        assert type(user['activated']) == bool
        assert type(user['created_at']) == str
        assert not hasattr(user, 'password')
        assert not hasattr(user, 'action_key')

    if 'acces' in extensions:
        assert user['acces']
        assert not hasattr(user, 'acces_id')


def test_getSelf():
    pass


@pytest.mark.parametrize('id, email, respCode', [
    (9, 'test@gmail.com', 200),
    (8, 'weronika1212@gmail.com', 200),
    (123, 'nonvalid@gmail.com', 404)
])
def test_getSpecificUser(id, email, respCode):
    resp = requests.get(f"{storage.url}/users/{id}", headers=storage.auth)
    json = resp.json()

    assert resp.status_code == respCode
    if respCode == 200:
        data = json['data']
        assert data['id'] == id
        assert data['email'] == email
    if respCode == 404:
        assert resp.status_code == 404


def test_updateUserInformations():
    uid = 8
    resp = requests.get(f"{storage.url}/users/{uid}", headers=storage.auth)
    assert resp.status_code == 200
    userSurname = resp.json()['data']['surname']

    resp = requests.patch(
        f"{storage.url}/users/{uid}",
        json={"surname": userSurname+"|T"},
        headers=storage.auth)
    
    if resp.status_code != 200:
        print(resp.json()["error"])
    assert resp.status_code == 200

    resp = requests.get(f"{storage.url}/users/{uid}", headers=storage.auth)
    assert resp.status_code == 200
    currentSurname = resp.json()["data"]["surname"]

    assert currentSurname != userSurname


def test_deleteUser():
    pass
